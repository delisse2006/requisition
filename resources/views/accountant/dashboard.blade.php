@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        
        @if(auth()->user()->isEmployee())
            <a href="{{ route('requisitions.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i> New Requisition
            </a>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-primary text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total Requests</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-warning text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-info text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['bought'] }}</h3>
                    <p class="text-muted mb-0">Bought</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-primary text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['done'] }}</h3>
                    <p class="text-muted mb-0">Done</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-success text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['paid'] }}</h3>
                    <p class="text-muted mb-0">Paid</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-circle bg-danger text-white mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['high_urgency'] }}</h3>
                    <p class="text-muted mb-0">High Urgency</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">Recent Requisitions</h4>
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
                            @if(auth()->user()->isEmployee())
                                <p>Click "New Requisition" to submit your first request.</p>
                            @else
                                <p>No requisitions match your current view.</p>
                            @endif
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
                                            @if(auth()->user()->isEmployee())
                                                <span class="text-muted">Me</span>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $req->user->avatar_small_url }}" 
                                                         alt="{{ $req->user->name }}" 
                                                         class="rounded-circle me-2" 
                                                         width="24" 
                                                         height="24"
                                                         style="object-fit: cover;">
                                                    <span>{{ $req->user->name }}</span>
                                                </div>
                                            @endif
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
                                            <div class="d-flex flex-wrap gap-2">
                                                {{-- Employee Actions --}}
                                                @if(auth()->user()->isEmployee())
                                                    @if($req->status === 'pending')
                                                        <a href="{{ route('requisitions.edit', $req) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Edit requisition">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('requisitions.destroy', $req) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this requisition?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Delete requisition">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($req->status === 'done' && !$req->received_confirmed)
                                                        <form action="{{ route('requisitions.confirm', $req) }}" 
                                                              method="POST" 
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-success" 
                                                                    title="Confirm receipt of items">
                                                                <i class="fas fa-check me-1"></i>Confirm Receipt
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                {{-- Accountant/Admin Actions --}}
                                                @if(auth()->user()->isAccountant() || auth()->user()->isAdmin())
                                                    @if(in_array($req->status, ['pending', 'bought', 'done']))
                                                        <a href="{{ route('requisitions.show', $req) }}" class="btn btn-sm btn-info" title="View / Update status">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- View details for everyone (authenticated) --}}
                                                <a href="{{ route('requisitions.show', $req) }}" class="btn btn-sm btn-outline-secondary" title="View details">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- View Receipt --}}
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

                                    {{-- Update Status Modal placeholder (rendered after table to avoid invalid HTML inside tbody) --}}
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Modals removed: View link navigates to `requisitions.show` for full details/update --}}

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
    will-change: transform;
    backface-visibility: hidden;
    transform: translateZ(0);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.icon-circle:hover {
    transform: translate3d(0, -2px, 0);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.card {
    will-change: transform;
    backface-visibility: hidden;
    transform: translateZ(0);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translate3d(0, -2px, 0);
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
@media (prefers-reduced-motion: reduce) {
    .icon-circle,
    .card,
    .table-hover tbody tr {
        transition: none !important;
        transform: none !important;
    }
}

@media (prefers-reduced-motion: reduce) {
    .icon-circle,
    .card,
    .table-hover tbody tr {
        transition: none !important;
        transform: none !important;
    }
}

</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced delete confirmation
    const deleteForms = document.querySelectorAll('form[method="POST"][onsubmit]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection