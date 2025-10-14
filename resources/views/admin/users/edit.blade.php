@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit User: {{ $user->name }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">User Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="employee" {{ $user->role == 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">
    <span class="spinner-border spinner-border-sm d-none" role="status" id="submitLoading"></span>
    <span id="submitText">Submit Requisition</span>
</button>

<script>
document.getElementById('submitBtn').addEventListener('click', function() {
    document.getElementById('submitLoading').classList.remove('d-none');
    document.getElementById('submitText').classList.add('d-none');
    this.disabled = true;
});
</script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection