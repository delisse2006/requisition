<?php

namespace App\Observers;

use App\Models\Requisition;
use App\Models\Notification;
use App\Models\User;

class RequisitionObserver
{
    /**
     * Handle the Requisition "created" event.
     */
    public function created(Requisition $requisition)
    {
        // Notify requester
        Notification::createUnique([
            'user_id' => $requisition->user_id,
            'type' => 'requisition_submitted',
            'title' => 'Requisition submitted',
            'message' => "Your requisition ({$requisition->requisition_no}) has been submitted.",
            'data' => ['requisition_id' => $requisition->id],
        ]);

        // Notify accountants (system/global notification could also be used)
        $accountants = User::where('role', 'accountant')->get();
        foreach ($accountants as $acc) {
            Notification::createUnique([
                'user_id' => $acc->id,
                'type' => 'requisition_submitted',
                'title' => 'New requisition submitted',
                'message' => "Requisition {$requisition->requisition_no} submitted by {$requisition->user->name}.",
                'data' => ['requisition_id' => $requisition->id],
            ]);
        }
    }

    /**
     * Handle the Requisition "updated" event.
     */
    public function updated(Requisition $requisition)
    {
        // If status changed, notify owner
        if ($requisition->wasChanged('status')) {
            Notification::createUnique([
                'user_id' => $requisition->user_id,
                'type' => 'status_updated',
                'title' => 'Requisition status updated',
                'message' => "Status changed to {$requisition->status}.",
                'data' => ['requisition_id' => $requisition->id, 'status' => $requisition->status],
            ]);
        }

        // If receipt uploaded or receipt_path changed, notify owner/accountant as needed
        if ($requisition->wasChanged('receipt_path')) {
            Notification::createUnique([
                'user_id' => $requisition->user_id,
                'type' => 'receipt_uploaded',
                'title' => 'Receipt uploaded',
                'message' => "A receipt was uploaded for {$requisition->requisition_no}.",
                'data' => ['requisition_id' => $requisition->id],
            ]);
        }
    }
}
