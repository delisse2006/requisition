@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h4>Security Verification</h4>
            </div>
            <div class="card-body">
                <p class="text-muted text-center mb-4">
                    To reset your password, please answer your security question:
                </p>
                
                <div class="alert alert-info">
                    <strong>{{ $securityQuestion }}</strong>
                </div>

                <form method="POST" action="{{ route('password.verify') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="mb-3">
                        <label>Your Answer</label>
                        <input type="password" name="security_answer" class="form-control" required>
                        @error('security_answer')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        Verify Answer
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}">← Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection