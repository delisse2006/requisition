<!-- resources/views/requisitions/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Requisition Details</h2>
            <p class="text-muted mb-0">{{ $requisition->requisition_no ?? 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <a href="{{ route('requisitions.receipt', $requisition) }}" 
               class="btn btn-outline-secondary {{ empty($requisition->receipt_path) ? 'disabled' : '' }}">
                <i class="fas fa-file-invoice me-1"></i>Receipt
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="mb-1">{{ $requisition->item_name }}</h4>
                        <div class="text-muted">Requested by {{ $requisition->user->name }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="fw-bold mb-2">Description</div>
                        <div class="text-secondary">{{ $requisition->description }}</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="text-muted small">Quantity</div>
                            <div class="fs-5">{{ $requisition->quantity }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Urgency</div>
                            <span class="badge bg-{{ $requisition->urgency == 'high' ? 'danger' : ($requisition->urgency == 'medium' ? 'warning' : 'success') }} rounded-pill px-3 py-2">
                                {{ ucfirst($requisition->urgency) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Status</div>
                            <span id="req_status_badge" class="badge bg-{{ $requisition->status == 'pending' ? 'secondary' : ($requisition->status == 'bought' ? 'info' : ($requisition->status == 'done' ? 'primary' : 'success')) }} rounded-pill px-3 py-2">
                                {{ ucfirst($requisition->status) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Received</div>
                            <span id="req_received_badge" class="badge bg-{{ $requisition->received_confirmed ? 'success' : 'secondary' }} rounded-pill px-3 py-2">
                                {{ $requisition->received_confirmed ? 'Confirmed' : 'Not confirmed' }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($requisition->notes))
                    <div class="mb-4">
                        <div class="fw-bold mb-2">Notes</div>
                        <div class="text-secondary">{{ $requisition->notes }}</div>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        @if(auth()->user()->isEmployee() && $requisition->status === 'done' && !$requisition->received_confirmed)
                            <form method="POST" action="{{ route('requisitions.confirm', $requisition) }}">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i>Confirm Receipt
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->isAccountant() || auth()->user()->isAdmin())
                                @if(in_array($requisition->status, ['pending', 'bought', 'done']))
                                    <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#updateStatusForm" aria-expanded="false" aria-controls="updateStatusForm">
                                        <i class="fas fa-sync-alt me-1"></i>Update Status
                                    </button>
                                @endif
                            @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Created</div>
                        <div>{{ $requisition->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Updated</div>
                        <div id="req_updated_at">{{ $requisition->updated_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Requisition No</div>
                        <div>{{ $requisition->requisition_no ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Receipt</div>
                        <div>
                            @if(!empty($requisition->receipt_path))
                                <a href="{{ route('requisitions.receipt', $requisition) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-file-invoice me-1"></i>Download
                                </a>
                            @else
                                <span class="text-muted">No receipt</span>
                            @endif
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(auth()->user()->isAccountant() || auth()->user()->isAdmin())
        @if(in_array($requisition->status, ['pending','bought','done']))
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="collapse" id="updateStatusForm">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-3">Update Requisition Status</h5>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="updateStatusFormEl" method="POST" action="{{ route('requisitions.update-status', $requisition) }}" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Next Status</label>
                                    <select name="status" class="form-select" required>
                                        @if($requisition->status == 'pending')
                                            <option value="bought">Bought</option>
                                        @elseif($requisition->status == 'bought')
                                            <option value="done">Done</option>
                                        @elseif($requisition->status == 'done')
                                            <option value="paid">Paid</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $requisition->notes) }}</textarea>
                                </div>

                                @if($requisition->status == 'done')
                                <div class="mb-3">
                                    <label class="form-label">Upload Payment Receipt</label>
                                    <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    @if($requisition->receipt_path)
                                        <div class="mt-2">
                                            <small class="text-muted">Current: <a href="{{ route('requisitions.receipt', $requisition) }}" target="_blank">View Receipt</a></small>
                                        </div>
                                    @endif
                                </div>
                                @endif

                                <div>
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#updateStatusForm">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('updateStatusFormEl');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';

        const fd = new FormData(form);
        // include X-Requested-With header when using fetch
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) {
                // try to parse JSON errors or show generic
                const text = await res.text();
                alert('Update failed.');
                console.error('Update failed response:', text);
                return;
            }

            // fetch updated requisition JSON
            const jsonUrl = "{{ route('requisitions.json', $requisition) }}";
            const r = await fetch(jsonUrl, { credentials: 'same-origin' });
            if (r.ok) {
                const data = await r.json();
                // update status badge
                const statusBadge = document.getElementById('req_status_badge');
                if (statusBadge) {
                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    // adjust classes
                    statusBadge.className = 'badge rounded-pill px-3 py-2';
                    if (data.status === 'pending') statusBadge.classList.add('bg-secondary');
                    else if (data.status === 'bought') statusBadge.classList.add('bg-info');
                    else if (data.status === 'done') statusBadge.classList.add('bg-primary');
                    else statusBadge.classList.add('bg-success');
                }

                // updated_at
                const updatedAt = document.getElementById('req_updated_at');
                if (updatedAt) updatedAt.textContent = data.updated_at || '';

                // received badge
                const rec = document.getElementById('req_received_badge');
                if (rec) {
                    if (data.received_confirmed) {
                        rec.textContent = 'Confirmed';
                        rec.className = 'badge bg-success rounded-pill px-3 py-2';
                    } else {
                        rec.textContent = 'Not confirmed';
                        rec.className = 'badge bg-secondary rounded-pill px-3 py-2';
                    }
                }

                // collapse the form
                const collapseEl = document.getElementById('updateStatusForm');
                if (collapseEl) {
                    const bs = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
                    bs.hide();
                }

                // show temporary success alert
                const container = document.querySelector('.container');
                const alert = document.createElement('div');
                alert.className = 'alert alert-success mt-3';
                alert.textContent = 'Status updated successfully!';
                container.prepend(alert);
                setTimeout(() => alert.remove(), 4000);
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred updating status. See console for details.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
});
</script>
@endpush