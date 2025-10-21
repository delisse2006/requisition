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
                
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-question-circle me-2"></i>Security Question</h6>
                    <p class="mb-0">{{ $securityQuestion }}</p>
                </div>

                <form method="POST" action="{{ route('password.verify') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="mb-3">
                        <label for="security_answer" class="form-label fw-semibold">Your Answer</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-key text-muted"></i>
                            </span>
                            <input type="password" 
                                   name="security_answer" 
                                   id="security_answer"
                                   class="form-control @error('security_answer') is-invalid @enderror border-start-0" 
                                   required
                                   placeholder="Enter your answer">
                        </div>
                        <div class="form-text">Answer is case-sensitive</div>
                        @error('security_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
@endsection