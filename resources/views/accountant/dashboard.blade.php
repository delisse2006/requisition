@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Accountant Dashboard</h2>
        <div class="stats-cards d-flex gap-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Pending</h5>
                    <h3 class="mb-0">{{ $requisitions->where('status', 'pending')->count() }}</h3>
                </div>
            </div>
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>Bought</h5>
                    <h3 class="mb-0">{{ $requisitions->where('status', 'bought')->count() }}</h3>
                </div>
            </div>
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Ready for Payment</h5>
                    <h3 class="mb-0">{{ $requisitions->where('status', 'done')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($requisitions->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
            <h4 class="mt-3">All requisitions are processed!</h4>
            <p class="text-muted">No pending actions required.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Req No</th>
                        <th>Item</th>
                        <th>Employee</th>
                        <th>Quantity</th>
                        <th>Urgency</th>
                        <th>Current Status</th>
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
                                {{ \Illuminate\Support\Str::limit($req->description, 40) }}
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
                                {{ $req->user->name }}
                            </div>
                        </td>
                        <td>{{ $req->quantity }}</td>
                        <td>
                            <span class="badge bg-{{ $req->urgency == 'high' ? 'danger' : ($req->urgency == 'medium' ? 'warning' : 'success') }} rounded-pill">
                                {{ ucfirst($req->urgency) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $req->status == 'pending' ? 'secondary' : ($req->status == 'paid' ? 'success' : 'info') }} rounded-pill">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td>
                            @if($req->status === 'pending')
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#updateModal{{ $req->id }}">
                                    <i class="fas fa-shopping-cart me-1"></i>Bought
                                </button>
                            @elseif($req->status === 'bought')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal{{ $req->id }}">
                                    <i class="fas fa-check-circle me-1"></i>Done
                                </button>
                            @elseif($req->status === 'done')
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#updateModal{{ $req->id }}">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>Paid
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Update Status Modal -->
                    <div class="modal fade" id="updateModal{{ $req->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('requisitions.update-status', $req) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            @if($req->status === 'pending') Update to Bought
                                            @elseif($req->status === 'bought') Mark as Done
                                            @elseif($req->status === 'done') Process Payment
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Requisition Details</label>
                                            <div class="alert alert-info">
                                                <strong>{{ $req->item_name }}</strong><br>
                                                Quantity: {{ $req->quantity }}<br>
                                                Current Status: <span class="badge bg-{{ $req->status == 'pending' ? 'secondary' : ($req->status == 'paid' ? 'success' : 'info') }}">{{ ucfirst($req->status) }}</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Notes (Optional)</label>
                                            <textarea name="notes" class="form-control" rows="3" placeholder="Add any relevant notes...">{{ $req->notes }}</textarea>
                                        </div>

                                        @if($req->status == 'done')
                                        <div class="mb-3">
                                            <label class="form-label">Upload Payment Receipt</label>
                                            <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                            @if($req->receipt_path)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        Current receipt: 
                                                        <a href="{{ asset('storage/'.$req->receipt_path) }}" target="_blank" class="text-decoration-underline">
                                                            View File
                                                        </a>
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn 
                                            @if($req->status === 'pending') btn-info
                                            @elseif($req->status === 'bought') btn-primary  
                                            @elseif($req->status === 'done') btn-success
                                            @endif">
                                            @if($req->status === 'pending') Mark as Bought
                                            @elseif($req->status === 'bought') Mark as Done
                                            @elseif($req->status === 'done') Process Payment
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $requisitions->links() }}
    @endif
</div>
@endsection