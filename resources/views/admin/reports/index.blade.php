@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Requisition Reports</h2>
        <a href="{{ route('admin.reports.export.pdf') }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-1"></i> Export PDF Report
        </a>
    </div>

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
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Req No</th>
                    <th>Item</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisitions as $r)
                <tr>
                    <td>{{ $r->requisition_no ?? 'N/A' }}</td>
                    <td>{{ $r->item_name }}</td>
                    <td>{{ $r->user->name }}</td>
                    <td>
                        <span class="badge bg-{{ $r->user->role == 'admin' ? 'danger' : ($r->user->role == 'accountant' ? 'info' : 'success') }}">
                            {{ ucfirst($r->user->role) }}
                        </span>
                    </td>
                    <td>{{ ucfirst($r->status) }}</td>
                    <td>{{ $r->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $requisitions->links() }}
</div>
@endsection