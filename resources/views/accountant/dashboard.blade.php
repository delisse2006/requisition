@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Accountant Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Requisitions -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-primary text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Requests</p>
                </div>
            </div>
        </div>
        
        <!-- Pending Requests -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-warning text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        
        <!-- Completed Requests (Paid) -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-success text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['paid'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Completed (Paid)</p>
                </div>
            </div>
        </div>
        
        <!-- This Month -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-info text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['this_month'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">This Month</p>
                </div>
            </div>
        </div>
        
        <!-- High Urgency -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-danger text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['high_urgency'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">High Urgency</p>
                </div>
            </div>
        </div>
        
        <!-- Back to Main Dashboard -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-secondary text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        <i class="fas fa-arrow-left me-1"></i>Main Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search item, description, or employee...">
                <select name="status" class="form-select" style="max-width: 180px;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="bought" {{ request('status') === 'bought' ? 'selected' : '' }}>Bought</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('accountant.dashboard') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Recent Requisitions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">Requisitions Requiring Action</h4>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($requisitions->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                            </div>
                            <h4 class="mt-3">No requisitions need your attention</h4>
                            <p>All pending, bought, and done requests are up to date.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="25%">Item</th>
                                        <th width="10%">Qty</th>
                                        <th width="15%">Urgency</th>
                                        <th width="15%">Status</th>
                                        <th width="20%">Requested By</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisitions as $req)
                                    {{-- ✅ Added title with notes for tooltip --}}
                                    <tr 
                                        class="{{ $req->urgency == 'high' ? 'table-danger' : ($req->urgency == 'medium' ? 'table-warning' : '') }}"
                                        title="{{ $req->notes ?: 'No notes' }}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                    >
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($req->receipt_path)
                                                        <i class="fas fa-file-invoice text-success"></i>
                                                    @else
                                                        <i class="fas fa-box text-muted"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $req->item_name }}</strong>
                                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($req->description, 40) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $req->quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $req->urgency == 'high' ? 'danger' : ($req->urgency == 'medium' ? 'warning' : 'success') }} text-white">
                                                {{ ucfirst($req->urgency) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'secondary',
                                                    'bought' => 'info',
                                                    'done' => 'primary',
                                                    'paid' => 'success'
                                                ];
                                                $statusColor = $statusColors[$req->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }} text-white">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                            @if($req->status === 'done' && $req->received_confirmed)
                                                <span class="badge bg-success ms-1" title="Employee confirmed receipt">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $req->user->avatar_small_url }}" 
                                                     alt="{{ $req->user->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="24" 
                                                     height="24"
                                                     style="object-fit: cover;">
                                                <span>{{ $req->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if(in_array($req->status, ['pending', 'bought', 'done']))
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateModal{{ $req->id }}"
                                                            title="Update Status">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                @endif

                                                @if($req->receipt_path)
                                                    <a href="{{ asset('storage/' . $req->receipt_path) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-secondary"
                                                       title="View Receipt">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Update Status Modal -->
                                    <div class="modal fade" id="updateModal{{ $req->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('requisitions.update-status', $req) }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update: {{ $req->item_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Next Status</label>
                                                            <select name="status" class="form-select" required>
                                                                @if($req->status == 'pending')
                                                                    <option value="bought">Bought</option>
                                                                @elseif($req->status == 'bought')
                                                                    <option value="done">Done</option>
                                                                @elseif($req->status == 'done')
                                                                    <option value="paid">Paid</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes (Optional)</label>
                                                            <textarea name="notes" class="form-control" rows="3">{{ $req->notes }}</textarea>
                                                        </div>
                                                        @if($req->status == 'done')
                                                        <div class="mb-3">
                                                            <label class="form-label">Upload Payment Receipt</label>
                                                            <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                            @if($req->receipt_path)
                                                                <div class="mt-2">
                                                                    <small class="text-muted">
                                                                        Current: 
                                                                        <a href="{{ asset('storage/'.$req->receipt_path) }}" target="_blank">View Receipt</a>
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-0 py-3">
                            {{ $requisitions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.icon-circle:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.025);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.70rem;
}

.badge {
    font-weight: 500;
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: false,
                trigger: 'hover'
            });
        });
    });
</script>
@endpush
@endsection