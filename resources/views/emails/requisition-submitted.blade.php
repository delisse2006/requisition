<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Requisition</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 20px auto;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h2 style="color: #28a745;">✅ Requisition Submitted Successfully</h2>
        <p>Dear <strong>{{ $requisition->user->name }}</strong>,</p>
        <p>Your requisition has been submitted and is now being processed:</p>
        
        <div style="background: white; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #28a745;">
            <p><strong>Requisition No:</strong> {{ $requisition->requisition_no }}</p>
            <p><strong>Item:</strong> {{ $requisition->item_name }}</p>
            <p><strong>Description:</strong> {{ $requisition->description }}</p>
            <p><strong>Quantity:</strong> {{ $requisition->quantity }}</p>
            <p><strong>Urgency:</strong> {{ ucfirst($requisition->urgency) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($requisition->status) }}</p>
        </div>

        <p>You will receive another email when the status changes.</p>
        <p>Thank you,<br><strong>Stock Requisition System</strong></p>
    </div>
</body>
</html>