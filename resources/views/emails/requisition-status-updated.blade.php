<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Status Updated</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 20px auto;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h2 style="color: #17a2b8;">🔄 Requisition Status Updated</h2>
        <p>Dear <strong>{{ $requisition->user->name }}</strong>,</p>
        <p>The status of your requisition has been updated by <strong>{{ $changedBy->name }}</strong>:</p>
        
        <div style="background: white; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #17a2b8;">
            <p><strong>Requisition No:</strong> {{ $requisition->requisition_no }}</p>
            <p><strong>Item:</strong> {{ $requisition->item_name }}</p>
            <p><strong>New Status:</strong> <span style="font-weight: bold; color: #28a745;">{{ ucfirst($requisition->status) }}</span></p>
            @if($requisition->notes)
                <p><strong>Notes:</strong> {{ $requisition->notes }}</p>
            @endif
        </div>

        <p>Log in to view details: <a href="{{ url('/dashboard') }}" style="color: #007bff;">View Dashboard</a></p>
        <p>Thank you,<br><strong>Stock Requisition System</strong></p>
    </div>
</body>
</html>