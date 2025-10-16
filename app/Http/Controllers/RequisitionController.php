<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisitionStatusUpdated; // ✅ Import your Mailable class

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

        // Generate requisition number
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
  public function accountantDashboard()
{
    // Statistics for accountant dashboard
    $stats = [
        'total' => Requisition::count(),
        'pending' => Requisition::where('status', 'pending')->count(),
        'bought' => Requisition::where('status', 'bought')->count(),
        'done' => Requisition::where('status', 'done')->count(),
    ];
    
    $query = Requisition::with('user')
        ->whereIn('status', ['pending', 'bought', 'done']);

    // Apply search filter
    if (request('search')) {
        $query->where(function($q) {
            $q->where('item_name', 'like', '%' . request('search') . '%')
              ->orWhere('description', 'like', '%' . request('search') . '%')
              ->orWhereHas('user', function($u) {
                  $u->where('name', 'like', '%' . request('search') . '%');
              });
        });
    }

    // Apply status filter
    if (request('status')) {
        $query->where('status', request('status'));
    }

    $requisitions = $query->latest()->paginate(15)->withQueryString();

    return view('accountant.dashboard', compact('requisitions', 'stats'));
}

    /**
     * Update requisition status (Accountant/Admin only).
     */
    public function updateStatus(Request $request, Requisition $requisition)
    {
        // Authorization check
        if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status'  => 'required|in:bought,done,paid',
            'notes'   => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Define valid transitions
        $validTransitions = [
            'pending' => ['bought'],
            'bought'  => ['done'],
            'done'    => ['paid'],
        ];

        $current = $requisition->status;
        $next = $request->status;

        // Enforce transition rule
        if (!in_array($next, $validTransitions[$current] ?? [])) {
            abort(422, 'Invalid status transition.');
        }

        $data = [
            'status' => $next,
            'notes'  => $request->notes,
        ];

        // Handle receipt upload (only when marking as "paid")
        if ($request->hasFile('receipt')) {
            if ($requisition->receipt_path) {
                Storage::disk('public')->delete($requisition->receipt_path);
            }

            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        $requisition->update($data);

        // Send email notification
        try {
            Mail::to($requisition->user->email)->send(
                new RequisitionStatusUpdated($requisition, auth()->user())
            );
        } catch (\Exception $e) {
            \Log::error('Mail sending failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Status updated successfully!');
    }
}
