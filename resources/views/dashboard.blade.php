@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Requisition Dashboard</h2>
        
        @if(auth()->user()->isEmployee())
           <a href="{{ route('requisitions.create') }}" class="btn btn-primary" id="newRequisitionBtn">
    <span class="spinner-border spinner-border-sm d-none" role="status" id="loadingSpinner"></span>
    <span id="buttonText"><i class="fas fa-plus me-1"></i> New Requisition</span>
</a>
        @endif
    </div>

    <!-- Search and Filter Form -->
    <form method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search items, descriptions, or users..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="bought" {{ request('status') == 'bought' ? 'selected' : '' }}>Bought</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="urgency" class="form-control">
                    <option value="">All Urgency</option>
                    <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i>Reset
                </a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
            <div class="mb-3">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
            </div>
            <h4>No requisitions found</h4>
            <p class="text-muted">
                @if(auth()->user()->isEmployee())
                    Click <strong>"New Requisition"</strong> to submit your first request.
                @else
                    No requisitions match your current view.
                @endif
            </p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Urgency</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requisitions as $req)
                    <tr>
                        <td>
                            <strong>{{ $req->item_name }}</strong>
                            <div class="text-muted small mt-1">
                                {{ \Illuminate\Support\Str::limit($req->description, 60) }}
                            </div>
                        </td>
                        <td>{{ $req->quantity }}</td>
                        <td>
                            @php
                                $urgencyMap = [
                                    'low' => 'success',
                                    'medium' => 'warning',
                                    'high' => 'danger'
                                ];
                                $urgencyColor = $urgencyMap[$req->urgency] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $urgencyColor }} rounded-pill px-3 py-2">
                                {{ ucfirst($req->urgency) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'pending' => 'secondary',
                                    'bought' => 'info',
                                    'done' => 'primary',
                                    'paid' => 'success'
                                ];
                                $statusColor = $statusMap[$req->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }} rounded-pill px-3 py-2">
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
                                        <button type="button" 
                                                class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateModal{{ $req->id }}"
                                                title="Update status">
                                            <i class="fas fa-sync-alt me-1"></i>Update
                                        </button>
                                    @endif
                                @endif

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

                    {{-- Update Status Modal --}}
                    @if(auth()->user()->isAccountant() || auth()->user()->isAdmin())
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
                                        <button type="submit" class="btn btn-primary">Update Status</button>
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

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $requisitions->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
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

@push('scripts')
<script>
document.getElementById('newRequisitionBtn').addEventListener('click', function(e) {
    // Show loading spinner
    document.getElementById('loadingSpinner').classList.remove('d-none');
    document.getElementById('buttonText').classList.add('d-none');
    this.classList.add('disabled');
    
    // Optional: Prevent double-click navigation
    setTimeout(() => {
        // If you want to allow navigation after 100ms
    }, 100);
});
</script>
@endpush
@endsection