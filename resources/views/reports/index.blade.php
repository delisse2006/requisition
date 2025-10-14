@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Requisition Reports</h2>

    <form method="GET" class="row mb-4">
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="bought" {{ request('status') == 'bought' ? 'selected' : '' }}>Bought</option>
                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="month" name="month" class="form-control" value="{{ request('month') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('reports.export.pdf') }}" class="btn btn-success">Export PDF</a>
        </div>
    </form>

    <table class="table table-striped">
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
</div>
@endsection