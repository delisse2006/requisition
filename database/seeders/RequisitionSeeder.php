<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requisition;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequisitionSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('receipts');

        // Get existing users
        $admin = User::where('email', 'admin@example.com')->first();
        $accountant = User::where('email', 'accountant@example.com')->first();
        $employee = User::where('email', 'employee@example.com')->first();
        $otherEmployees = User::where('role', 'employee')->whereNotIn('email', [
            'employee@example.com'
        ])->get();

        // If users don't exist, create minimal ones
        if (!$employee) {
            $employee = User::factory()->create(['role' => 'employee']);
        }

        // Create sample requisitions
        $requisitions = [
            // Pending (needs action)
            [
                'user_id' => $employee->id,
                'item_name' => 'Dell Laptop',
                'description' => 'For new hire in Marketing department',
                'quantity' => 2,
                'urgency' => 'high',
                'status' => 'pending',
                'notes' => 'Urgent - needed by Monday',
            ],
            [
                'user_id' => $otherEmployees->first()?->id ?? $employee->id,
                'item_name' => 'Ergonomic Chair',
                'description' => 'Replacement for broken chair in Dev team',
                'quantity' => 1,
                'urgency' => 'medium',
                'status' => 'pending',
                'notes' => null,
            ],

            // Bought
            [
                'user_id' => $employee->id,
                'item_name' => 'Logitech Mouse',
                'description' => 'Bulk order for IT department',
                'quantity' => 20,
                'urgency' => 'low',
                'status' => 'bought',
                'notes' => 'Vendor confirmed delivery next week',
            ],

            // Done (awaiting confirmation or payment)
            [
                'user_id' => $employee->id,
                'item_name' => 'HP Printer',
                'description' => 'For HR office',
                'quantity' => 1,
                'urgency' => 'medium',
                'status' => 'done',
                'notes' => 'Delivered and installed. Awaiting payment.',
                'received_confirmed' => true,
            ],

            // Paid (completed)
            [
                'user_id' => $employee->id,
                'item_name' => 'Whiteboard',
                'description' => 'For meeting room 3',
                'quantity' => 2,
                'urgency' => 'low',
                'status' => 'paid',
                'notes' => 'Invoice #INV-2025-001 paid on 2025-10-10',
                'receipt_path' => 'receipts/sample_receipt.pdf', // Simulated
            ],
        ];

        foreach ($requisitions as $req) {
            $requisition = Requisition::create($req);

            // Generate requisition number
            $requisition->update([
                'requisition_no' => 'REQ-' . now()->format('Y') . '-' . str_pad($requisition->id, 5, '0', STR_PAD_LEFT)
            ]);
        }

        // Create 10 additional random requisitions
        Requisition::factory(10)->create();

        // Simulate a real receipt file (optional)
        $receiptContent = "Sample Payment Receipt\nInvoice: INV-2025-001\nAmount: $450.00";
        Storage::disk('public')->put('receipts/sample_receipt.pdf', $receiptContent);
    }
}