<?php

namespace App\Mail;

use App\Models\Requisition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequisitionStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    public $changedBy;

    public function __construct(Requisition $requisition, $changedBy)
    {
        $this->requisition = $requisition;
        $this->changedBy = $changedBy;
    }

    public function build()
    {
        return $this->subject('🔄 Requisition Status Updated - ' . $this->requisition->item_name)
                    ->view('emails.requisition-status-updated');
    }
}