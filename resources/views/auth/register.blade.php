@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required 
                               minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role Field -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" 
                                id="role" 
                                class="form-control @error('role') is-invalid @enderror" 
                                required>
                            <option value="">Select Role</option>
                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="accountant" {{ old('role') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ✅ Security Question Field -->
                    <div class="mb-3">
                        <label for="security_question" class="form-label">Security Question</label>
                        <select name="security_question" 
                                id="security_question" 
                                class="form-control @error('security_question') is-invalid @enderror" 
                                required>
                            <option value="">Select a security question</option>
                            @foreach(\App\Models\User::getSecurityQuestions() as $question)
                                <option value="{{ $question }}" {{ old('security_question') == $question ? 'selected' : '' }}>
                                    {{ $question }}
                                </option>
                            @endforeach
                        </select>
                        @error('security_question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ✅ Security Answer Field -->
                    <div class="mb-3">
                        <label for="security_answer" class="form-label">Security Answer</label>
                        <input type="text" 
                               name="security_answer" 
                               id="security_answer" 
                               class="form-control @error('security_answer') is-invalid @enderror" 
                               value="{{ old('security_answer') }}" 
                               placeholder="Enter your answer"
                               required 
                               minlength="3">
                        <div class="form-text">Remember this answer - you'll need it if you forget your password.</div>
                        @error('security_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-link">
                            <i class="fas fa-sign-in-alt me-2"></i>Already have an account?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection