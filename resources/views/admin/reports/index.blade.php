@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Requisition Reports</h2>
        <a href="{{ route('admin.reports.export.pdf') }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>

    <!-- Filters -->
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
            <select name="urgency" class="form-control">
                <option value="">All Urgency</option>
                <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>High</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="month" name="month" class="form-control" value="{{ request('month') }}">
        </div>
        <div class="col-md-3">
            <div class="d-grid gap-2 d-md-flex">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($requisitions->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
            <h4 class="mt-3">No requisitions found</h4>
            <p>Adjust your filters or submit new requisitions.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Req No</th>
                        <th>Item</th>
                        <th>User</th>
                        <th>Qty</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requisitions as $req)
                    <tr>
                        <td>{{ $req->requisition_no ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $req->item_name }}</strong>
                            <div class="text-muted small mt-1">
                                {{ \Illuminate\Support\Str::limit($req->description, 50) }}
                            </div>
                        </td>
                        <td>{{ $req->user->name }}</td>
                        <td>{{ $req->quantity }}</td>
                        <td>
                            <span class="badge bg-{{ $req->urgency == 'high' ? 'danger' : ($req->urgency == 'medium' ? 'warning' : 'success') }} rounded-pill px-3 py-2">
                                {{ ucfirst($req->urgency) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $req->status == 'pending' ? 'secondary' : ($req->status == 'bought' ? 'info' : ($req->status == 'done' ? 'primary' : 'success')) }} rounded-pill px-3 py-2">
                                {{ ucfirst($req->status) }}
                            </span>
                            @if($req->status === 'done' && $req->received_confirmed)
                                <span class="badge bg-success rounded-pill px-3 py-2 ms-1">
                                    <i class="fas fa-check-circle me-1"></i>Received
                                </span>
                            @endif
                        </td>
                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preserve filter parameters when paginating
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        const url = new URL(link.href);
        const params = new URLSearchParams(window.location.search);

        // Add current filters to pagination links
        for (const [key, value] of params) {
            if (value && !url.searchParams.has(key)) {
                url.searchParams.append(key, value);
            }
        }

        link.href = url.toString();
    });
});
</script>
@endpush
@endsection