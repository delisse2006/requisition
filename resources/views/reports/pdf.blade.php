<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Requisition Report</title>
    <style>
        /* Use a font that supports UTF-8 and works in dompdf */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #666;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #2c3e50;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <h2>Requisition Report</h2>

    @if($requisitions->isEmpty())
        <p class="text-center">No requisitions found.</p>
    @else
        <table>
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
                    <td>{{ $r->requisition_no ?? '—' }}</td>
                    <td>{{ $r->item_name }}</td>
                    <td>{{ $r->user->name ?? 'Unknown' }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                    <td>{{ $r->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generated on {{ now()->format('F j, Y \a\t g:i A') }}
    </div>
</body>
</html>