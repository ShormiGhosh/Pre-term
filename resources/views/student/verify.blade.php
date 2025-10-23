@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="card">
    <h1 class="card-title">Verify Your Email</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if(session('verification_code'))
        <div class="alert alert-success">
            <strong>For Development/Testing:</strong> Your verification code is: <strong>{{ session('verification_code') }}</strong>
        </div>
    @endif

    <p style="color: #666; margin-bottom: 1.5rem; text-align: center;">
        We've sent a 6-digit verification code to<br>
        <strong>{{ session('pending_verification_email') }}</strong>
    </p>

    <form action="{{ route('student.verify.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="code" class="form-label">Enter Verification Code</label>
            <input type="text" id="code" name="code" class="form-input" maxlength="6" placeholder="000000" required autofocus style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
            @error('code')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Verify Email</button>
    </form>

    <div class="text-center mt-3">
        <p>Didn't receive the code?</p>
        <form action="{{ route('student.verify.resend') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="link" style="background: none; border: none; cursor: pointer; font-size: 1rem;">Resend Code</button>
        </form>
    </div>
</div>

<script>
// Auto-format code input
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
