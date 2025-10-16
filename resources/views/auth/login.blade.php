@extends('layouts.app')

@section('content')
<div class="d-flex min-vh-100">
    <!-- Left Side - Brand Section -->
    <div class="col-md-6 d-none d-md-flex flex-column justify-content-center align-items-center text-white p-5" style="background: linear-gradient(135deg, #1a2a6c, #2a4d69);">
        <div class="text-center mb-5">
            <div class="brand-icon mb-4" style="width: 100px; height: 100px; background: rgba(255, 255, 255, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-warehouse" style="font-size: 3rem;"></i>
            </div>
            <h2 class="display-6 fw-bold mb-3">Stock Requisition System</h2>
            <p class="lead opacity-90">Streamline your internal stock management with our professional solution</p>
        </div>
        
        <div class="features text-center">
            <div class="d-flex gap-4 mb-4">
                <div class="feature-item">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p>Real-time Tracking</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt fa-2x mb-2"></i>
                    <p>Secure Workflow</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <p>Analytics</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Login Form -->
    <div class="col-md-6 d-flex align-items-center justify-content-center p-4 p-md-5">
        <div class="login-container w-100" style="max-width: 450px;">
            <div class="text-center mb-5">
                <h3 class="fw-bold text-dark">Welcome Back</h3>
                <p class="text-muted">Sign in to continue to your dashboard</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="animate__animated animate__fadeInUp">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-envelope text-muted"></i>
                        </span>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror border-start-0" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-lock text-muted"></i>
                        </span>
                        <input type="password" 
                               class="form-control border-start-0" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
</div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted mb-0">Don't have an account?</p>
                <a href="{{ route('register') }}" class="btn btn-outline-primary mt-2 px-4">
                    <i class="fas fa-user-plus me-1"></i>Create Account
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.brand-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.feature-item {
    opacity: 0.9;
    transition: opacity 0.3s ease;
}

.feature-item:hover {
    opacity: 1;
}

.input-group-text {
    border-radius: 8px 0 0 8px !important;
}

.form-control {
    border-radius: 0 8px 8px 0 !important;
    padding: 12px 16px;
}

.login-container {
    animation: slideInRight 0.6s ease-out;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Responsive design */
@media (max-width: 768px) {
    .min-vh-100 {
        flex-direction: column;
    }
    
    .brand-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 2rem;
    }
    
    .features {
        display: none;
    }
}
</style>
@endsection