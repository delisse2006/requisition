<h2>Requisition Report</h2>
<table border="1" width="100%">
    <thead>
        <tr>
            <th>Requisition No</th>
            <th>Item</th>
            <th>User</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requisitions as $r)
        <tr>
            <td>{{ $r->requisition_no }}</td>
            <td>{{ $r->item_name }}</td>
            <td>{{ $r->user->name }}</td>
            <td>{{ ucfirst($r->status) }}</td>
            <td>{{ $r->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>