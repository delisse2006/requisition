<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisitionStatusUpdated;
use Carbon\Carbon;

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
            'item_name'   => 'required|string|max:255',
            'description' => 'required|string',
            'quantity'    => 'required|integer|min:1',
            'urgency'     => 'required|in:low,medium,high',
        ]);

        $requisition = auth()->user()->requisitions()->create(
            $request->only(['item_name', 'description', 'quantity', 'urgency'])
        );

        // Generate unique requisition number
        $requisition->update([
            'requisition_no' => 'REQ-' . now()->format('Y') . '-' . str_pad($requisition->id, 5, '0', STR_PAD_LEFT)
        ]);

        return redirect()->route('dashboard')->with('success', 'Requisition submitted successfully!');
    }

    /**
     * Edit a pending requisition.
     */
    public function edit(Requisition $requisition)
    {
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
        if (auth()->id() !== $requisition->user_id || $requisition->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'item_name'   => 'required|string|max:255',
            'description' => 'required|string',
            'quantity'    => 'required|integer|min:1',
            'urgency'     => 'required|in:low,medium,high',
        ]);

        $requisition->update($request->only(['item_name', 'description', 'quantity', 'urgency']));

        return redirect()->route('dashboard')->with('success', 'Requisition updated successfully!');
    }

    /**
     * Delete a pending requisition.
     */
    public function destroy(Requisition $requisition)
    {
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
        if (auth()->id() !== $requisition->user_id) {
            abort(403);
        }

        if ($requisition->status !== 'done' || $requisition->received_confirmed) {
            abort(403, 'You can only confirm receipt for completed items.');
        }

        $requisition->update(['received_confirmed' => true]);

        return back()->with('success', 'Receipt confirmed!');
    }

    /**
     * Accountant dashboard view (filter + search).
     */
    public function accountantDashboard(Request $request)
    {
        // ✅ Statistics
        $stats = [
            'total' => Requisition::count(),
            'pending' => Requisition::where('status', 'pending')->count(),
            'bought' => Requisition::where('status', 'bought')->count(),
            'done' => Requisition::where('status', 'done')->count(),
            'paid' => Requisition::where('status', 'paid')->count(),
            'this_month' => Requisition::whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count(),
            'high_urgency' => Requisition::where('urgency', 'high')->count(),
        ];

        // ✅ Query for actionable requisitions
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

        // ✅ RETURN THE VIEW (this was missing!)
        return view('accountant.dashboard', compact('requisitions', 'stats'));
    }

    /**
     * Update requisition status (Accountant/Admin only).
     */
    public function updateStatus(Request $request, Requisition $requisition)
    {
        if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status'  => 'required|in:bought,done,paid',
            'notes'   => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $validTransitions = [
            'pending' => ['bought'],
            'bought'  => ['done'],
            'done'    => ['paid'],
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

        // Handle receipt only when moving to 'paid'
        if ($next === 'paid' && $request->hasFile('receipt')) {
            if ($requisition->receipt_path) {
                Storage::disk('public')->delete($requisition->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        if ($next !== 'paid' && $request->hasFile('receipt')) {
            return back()->withErrors(['receipt' => 'Receipts can only be uploaded when marking as Paid.']);
        }

        $requisition->update($data);

        // Optional email
        try {
            Mail::to($requisition->user->email)->send(
                new RequisitionStatusUpdated($requisition, auth()->user())
            );
        } catch (\Exception $e) {
            \Log::warning('Email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Status updated successfully!');
    }
}
