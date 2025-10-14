@extends('layouts.app')

@section('content')
<style>
    /* Override main background for login page */
    body {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                    url('{{ asset('images/backgrounds/login-bg.jpg') }}') center center no-repeat fixed;
        background-size: cover;
        min-height: 100vh;
        padding-top: 0 !important;
    }
    
    .login-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.4);
    }
    
    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
    }
</style>

<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-6">
        <div class="login-container">
            <div class="card border-0">
                <div class="card-header login-header text-center">
                    <h3><i class="fas fa-boxes me-2"></i>Stock Requisition System</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            <i class="fas fa-user-plus me-1"></i>Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection