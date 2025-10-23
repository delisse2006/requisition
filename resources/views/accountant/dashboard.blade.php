@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Accountant Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('reports.pdf') }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
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
        
        <!-- Bought Requests -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-info text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['bought'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Bought</p>
                </div>
            </div>
        </div>
        
        <!-- Done Requests -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-primary text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['done'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Done</p>
                </div>
            </div>
        </div>
        
        <!-- Paid Requests -->
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-success text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['paid'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Paid</p>
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
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search item, description, or employee...">
                <select name="status" class="form-select" style="max-width: 180px;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="bought" {{ request('status') == 'bought' ? 'selected' : '' }}>Bought</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($requisitions->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                            </div>
                            <h4 class="mt-3">No requisitions found</h4>
                            <p>No requisitions match your current view.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Req No</th>
                                        <th>Item</th>
                                        <th>User</th>
                                        <th>Qty</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisitions as $req)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $req->requisition_no ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $req->item_name }}</strong>
                                            <div class="text-muted small mt-1">
                                                {{ \Illuminate\Support\Str::limit($req->description, 50) }}
                                            </div>
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
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if(in_array($req->status, ['pending', 'bought', 'done']))
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateModal{{ $req->id }}"
                                                            title="Update status">
                                                        <i class="fas fa-sync-alt me-1"></i>Update
                                                    </button>
                                                @endif

                                                @if($req->receipt_path)
                                                    <a href="{{ asset('storage/' . $req->receipt_path) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-secondary"
                                                       title="View receipt">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Update Status Modal -->
                                    @if(in_array($req->status, ['pending', 'bought', 'done']))
                                    <div class="modal fade" id="updateModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('requisitions.update-status', $req) }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update: {{ $req->item_name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $req->notes) }}</textarea>
                                                        </div>

                                                        @if($req->status == 'done')
                                                        <div class="mb-3">
                                                            <label class="form-label">Upload Payment Receipt</label>
                                                            <input type="file" 
                                                                   name="receipt" 
                                                                   class="form-control" 
                                                                   accept=".pdf,.jpg,.jpeg,.png">
                                                            @if($req->receipt_path)
                                                                <div class="mt-2">
                                                                    <small class="text-muted">
                                                                        Current: 
                                                                        <a href="{{ asset('storage/'.$req->receipt_path) }}" 
                                                                           target="_blank">
                                                                            View Receipt
                                                                        </a>
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <span class="spinner-border spinner-border-sm d-none" role="status" id="updateSpinner{{ $req->id }}"></span>
                                                            <span id="updateText{{ $req->id }}">Update Status</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
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
    font-size: 0.75rem;
}

.badge {
    font-weight: 500;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to all update forms
    @foreach($requisitions as $req)
        @if(in_array($req->status, ['pending', 'bought', 'done']))
        const form{{ $req->id }} = document.querySelector('form[action="{{ route('requisitions.update-status', $req) }}"]');
        if (form{{ $req->id }}) {
            form{{ $req->id }}.addEventListener('submit', function() {
                const spinner = document.getElementById('updateSpinner{{ $req->id }}');
                const text = document.getElementById('updateText{{ $req->id }}');
                const submitBtn = this.querySelector('button[type="submit"]');
                
                if (spinner && text) {
                    spinner.classList.remove('d-none');
                    text.classList.add('d-none');
                    submitBtn.disabled = true;
                }
            });
        }
        @endif
    @endforeach
    
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