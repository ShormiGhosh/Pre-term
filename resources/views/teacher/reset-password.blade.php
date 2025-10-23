@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="card">
    <h1 class="card-title">Set New Password</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <p style="color: #666; margin-bottom: 1.5rem; text-align: center;">
        Enter your new password below.
    </p>

    <form action="{{ route('teacher.reset.password.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" class="form-input" style="padding-right: 45px;" required autofocus>
                <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; padding: 0; display: flex; align-items: center;">
                    <span class="material-icons" style="font-size: 20px;">visibility_off</span>
                </button>
            </div>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <div style="position: relative;">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" style="padding-right: 45px;" required>
                <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; padding: 0; display: flex; align-items: center;">
                    <span class="material-icons" style="font-size: 20px;">visibility_off</span>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
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
