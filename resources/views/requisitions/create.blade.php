@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Submit New Requisition</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requisitions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Item Name</label>
                        <input type="text" name="item_name" class="form-control" value="{{ old('item_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                    </div>
                    <!-- Add after description field -->
<div class="mb-3">
    <label class="form-label">Item Image (Optional)</label>
    <input type="file" name="item_image" accept="image/*" class="form-control">
    <small class="text-muted">Max 2MB, JPG/PNG only</small>
</div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Urgency</label>
                        <select name="urgency" class="form-control" required>
                            <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('urgency') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Submit Requisition</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection