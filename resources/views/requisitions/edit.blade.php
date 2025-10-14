@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Edit Requisition</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requisitions.update', $requisition) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label>Item Name</label>
                        <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $requisition->item_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ old('description', $requisition->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $requisition->quantity) }}" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Urgency</label>
                        <select name="urgency" class="form-control" required>
                            <option value="low" {{ $requisition->urgency == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $requisition->urgency == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $requisition->urgency == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Requisition</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection