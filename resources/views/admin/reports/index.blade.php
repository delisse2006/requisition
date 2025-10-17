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
@endsection@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Requisition Reports</h2>
            <p class="text-muted mb-0">Manage and export requisition data</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Summary Report Button -->
            <a href="{{ route('admin.reports.summary') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-1"></i> Summary
            </a>
            <!-- PDF Export Button -->
            <a href="{{ route('admin.reports.pdf') }}" class="btn btn-success">
                <i class="fas fa-file-pdf me-1"></i> Export All PDF
            </a>
        </div>
    </div>

    <!-- Filters: Status + Date Range -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="bought" {{ request('status') == 'bought' ? 'selected' : '' }}>Bought</option>
                        <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">From</label>
                    <input type="date" name="start" class="form-control" value="{{ request('start') }}">
                </div>

                <!-- End Date -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">To</label>
                    <input type="date" name="end" class="form-control" value="{{ request('end') }}">
                </div>

                <!-- Actions -->
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Filtered PDF Button (if filters applied) -->
    @if(request()->anyFilled(['status', 'start', 'end']))
        <div class="mb-4 text-end">
            <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="btn btn-info">
                <i class="fas fa-file-export me-1"></i> Export Filtered PDF
            </a>
        </div>
    @endif

    <!-- Requisitions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($requisitions->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h4 class="mt-3">No requisitions found</h4>
                    <p class="text-muted">Try adjusting your filters.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Req No</th>
                                <th>Item</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Urgency</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisitions as $r)
                            <tr>
                                <td><code>{{ $r->requisition_no ?? '—' }}</code></td>
                                <td>{{ $r->item_name }}</td>
                                <td>{{ $r->user->name ?? 'Unknown' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $r->user->role == 'admin' ? 'danger' : 
                                        ($r->user->role == 'accountant' ? 'info' : 'success') 
                                    }} text-white">
                                        {{ ucfirst($r->user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $r->status == 'pending' ? 'secondary' :
                                        ($r->status == 'bought' ? 'info' :
                                        ($r->status == 'done' ? 'primary' : 'success'))
                                    }} text-white">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $r->urgency == 'high' ? 'danger' :
                                        ($r->urgency == 'medium' ? 'warning' : 'success')
                                    }} text-white">
                                        {{ ucfirst($r->urgency) }}
                                    </span>
                                </td>
                                <td>{{ $r->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-0 py-3">
                    {{ $requisitions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection