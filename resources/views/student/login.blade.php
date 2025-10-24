@extends('layouts.app')

@section('title', 'Student Login')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="card">
    <h1 class="card-title">Student Login</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('student.login.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" class="form-input" style="padding-right: 45px;" required>
                <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #C1CEE5; padding: 0; display: flex; align-items: center;">
                    <span class="material-icons" style="font-size: 20px;">visibility_off</span>
                </button>
            </div>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="text-center mt-3">
        <p>Don't have an account? <a href="{{ route('student.signup') }}" class="link">Sign up here</a></p>
        <p><a href="{{ route('student.forgot-password') }}" class="link">Forgot Password?</a></p>
        <p><a href="{{ route('home') }}" class="link">Back to Home</a></p>
    </div>
</div>

<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('.material-icons');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.textContent = 'visibility';
    } else {
        field.type = 'password';
        icon.textContent = 'visibility_off';
    }
}
</script>
@endsection

