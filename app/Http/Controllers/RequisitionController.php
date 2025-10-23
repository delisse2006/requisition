<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisitionSubmitted;
use App\Mail\RequisitionStatusUpdated;

class RequisitionController extends Controller
{
    /**
     * Show the requisition creation form.
     */
    public function create()
    {
        return view('requisitions.create');
    }

    /**
     * Store a new requisition in the database.
     */
   public function store(Request $request)
{
    $request->validate([
        'item_name' => 'required|string|max:255',
        'description' => 'required|string',
        'quantity' => 'required|integer|min:1',
        'urgency' => 'required|in:low,medium,high',
    ]);

    // ✅ CREATE REQUISITION WITH CURRENT USER
    $requisition = auth()->user()->requisitions()->create(
        $request->only(['item_name', 'description', 'quantity', 'urgency'])
    );

    // Generate unique requisition number
    $requisition->update([
        'requisition_no' => 'REQ-' . now()->format('Y') . '-' . str_pad($requisition->id, 5, '0', STR_PAD_LEFT)
    ]);

    // ✅ SEND EMAIL NOTIFICATIONS (WITH ERROR HANDLING)
    try {
        // Notify employee
        Mail::to($requisition->user->email)->send(new RequisitionSubmitted($requisition));

        // Notify all accountants
        $accountants = User::where('role', 'accountant')->get();
        foreach ($accountants as $accountant) {
            Mail::to($accountant->email)->send(new RequisitionSubmitted($requisition));
        }
    } catch (\Exception $e) {
        // Log error but don't stop the process
        \Log::warning('Email notification failed: ' . $e->getMessage());
        // Continue without emails
    }

    return redirect()->route('dashboard')->with('success', 'Requisition submitted successfully!');
}

    /**
     * Show the requisition edit form.
     */
    public function edit(Requisition $requisition)
    {
        // ✅ SECURITY: Only owner can edit, and only if pending
        if (auth()->id() !== $requisition->user_id || $requisition->status !== 'pending') {
            abort(403, 'You can only edit your own pending requisitions.');
        }

        return view('requisitions.edit', compact('requisition'));
    }

    /**
     * Update a pending requisition.
     */
    public function update(Request $request, Requisition $requisition)
    {
        // ✅ SECURITY: Re-check authorization
        if (auth()->id() !== $requisition->user_id || $requisition->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'urgency' => 'required|in:low,medium,high',
        ]);

        $requisition->update($request->only(['item_name', 'description', 'quantity', 'urgency']));
        return redirect()->route('dashboard')->with('success', 'Requisition updated successfully!');
    }

    /**
     * Delete a pending requisition.
     */
    public function destroy(Requisition $requisition)
    {
        // ✅ SECURITY: Only owner can delete pending requisitions
        if (auth()->id() !== $requisition->user_id || $requisition->status !== 'pending') {
            abort(403);
        }

        $requisition->delete();
        return redirect()->route('dashboard')->with('success', 'Requisition deleted successfully!');
    }

    /**
     * Confirm receipt by requester once requisition is done.
     */
    public function confirmReceipt(Requisition $requisition)
    {
        // ✅ SECURITY: Only the requester can confirm
        if (auth()->id() !== $requisition->user_id) {
            abort(403);
        }

        // ✅ VALIDATION: Only confirm if status is 'done' and not already confirmed
        if ($requisition->status !== 'done' || $requisition->received_confirmed) {
            abort(403, 'You can only confirm receipt for completed items that haven\'t been confirmed yet.');
        }

        $requisition->update(['received_confirmed' => true]);
        return back()->with('success', 'Receipt confirmed! Thank you.');
    }

    /**
     * Accountant dashboard view (filter + search).
     */
    public function accountantDashboard(Request $request)
    {
        // ✅ STATISTICS
        $stats = [
            'total' => Requisition::count(),
            'pending' => Requisition::where('status', 'pending')->count(),
            'bought' => Requisition::where('status', 'bought')->count(),
            'done' => Requisition::where('status', 'done')->count(),
            'paid' => Requisition::where('status', 'paid')->count(),
        ];

        // ✅ QUERY FOR ACTIONABLE REQUISITIONS
        $query = Requisition::with('user')
            ->whereIn('status', ['pending', 'bought', 'done']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($request->filled('status') && in_array($request->status, ['pending', 'bought', 'done'])) {
            $query->where('status', $request->status);
        }

        $requisitions = $query->latest()->paginate(15)->withQueryString();

        return view('accountant.dashboard', compact('requisitions', 'stats'));
    }

    /**
     * Update requisition status (Accountant/Admin only).
     */
    public function updateStatus(Request $request, Requisition $requisition)
    {
        // ✅ SECURITY: Only accountants and admins can update status
        if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // ✅ VALIDATION
        $request->validate([
            'status' => 'required|in:bought,done,paid',
            'notes' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // ✅ ENFORCE STATUS WORKFLOW
        $validTransitions = [
            'pending' => ['bought'],
            'bought'  => ['done'],
            'done'    => ['paid'],
            'paid'    => [], // no further changes
        ];

        $current = $requisition->status;
        $next = $request->status;

        if (!in_array($next, $validTransitions[$current] ?? [])) {
            return back()->withErrors(['status' => "Invalid transition from {$current} to {$next}."]);
        }

        $data = [
            'status' => $next,
            'notes'  => $request->notes,
        ];

        // ✅ HANDLE RECEIPT UPLOAD (only when moving to 'paid')
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($requisition->receipt_path) {
                Storage::disk('public')->delete($requisition->receipt_path);
            }

            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        // ✅ UPDATE REQUISITION
        $requisition->update($data);

        // ✅ SEND EMAIL NOTIFICATION TO EMPLOYEE
        try {
            Mail::to($requisition->user->email)->send(
                new RequisitionStatusUpdated($requisition, auth()->user())
            );
        } catch (\Exception $e) {
            \Log::warning('Email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Status updated successfully!');
    }

    /**
     * Download the uploaded receipt (accountant uploaded file).
     */
    public function downloadReceipt(Requisition $requisition)
    {
        // ✅ SECURITY: Allow owner, accountant, admin
        $user = auth()->user();
        if (
            $user->id !== $requisition->user_id &&
            !$user->isAccountant() &&
            !$user->isAdmin()
        ) {
            abort(403);
        }

        if (empty($requisition->receipt_path) || !Storage::disk('public')->exists($requisition->receipt_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($requisition->receipt_path);
    }
}
