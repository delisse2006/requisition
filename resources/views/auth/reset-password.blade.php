@extends('layouts.app')

@section('content')
<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h4>Reset Password</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        Reset Password
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