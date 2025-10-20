@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-shield-alt me-2"></i>Security Verification</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-lock" style="font-size: 3rem; color: #17a2b8;"></i>
                    </div>
                    <p class="text-muted">
                        To protect your account, please answer your security question:
                    </p>
                </div>
                
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

                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-question-circle me-2"></i>Security Question</h6>
                    <p class="mb-0">{{ $securityQuestion }}</p>
                </div>

                <form method="POST" action="{{ route('password.verify') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="mb-4">
                        <label for="security_answer" class="form-label fw-semibold">Your Answer</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-key text-muted"></i>
                            </span>
                            <input type="password" 
                                   name="security_answer" 
                                   id="security_answer"
                                   class="form-control border-start-0 @error('security_answer') is-invalid @enderror" 
                                   required
                                   placeholder="Enter your answer"
                                   autocomplete="off">
                            @error('security_answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Answer is case-sensitive</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                        <i class="fas fa-check-circle me-2"></i>Verify Answer
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-times me-1"></i>Cancel
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

.btn-primary {
    padding: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.alert {
    border-radius: 10px;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1565c0;
}

.form-control:focus {
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

.alert-heading {
    font-weight: 600;
}
</style>
@endsection
