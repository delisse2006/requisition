@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-unlock-alt me-2"></i>Reset Password</h4>
            </div>
            <div class="card-body">
                <p class="text-muted text-center mb-4">
                    Enter your new password below.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                   required 
                                   minlength="8"
                                   placeholder="Enter new password"
                                   autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Password must be at least 8 characters long</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="form-control border-start-0" 
                                   required
                                   placeholder="Confirm new password"
                                   autocomplete="new-password">
                        </div>
                        <div class="form-text">Must match your new password</div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold">
                        <i class="fas fa-sync-alt me-2"></i>Reset Password
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

.input-group-text {
    border-radius: 8px 0 0 8px !important;
}

.form-control {
    border-radius: 0 8px 8px 0 !important;
    padding: 12px 16px;
}

.btn-success {
    padding: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.alert {
    border-radius: 10px;
}

.form-control:focus {
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
    border-color: #28a745;
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
}
</style>
@endsection
