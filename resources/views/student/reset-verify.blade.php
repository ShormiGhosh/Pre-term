@extends('layouts.app')

@section('title', 'Verify Reset Code')

@section('content')
<div class="card">
    <h1 class="card-title">Verify Reset Code</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if(session('verification_code'))
        <div class="alert alert-success">
            <strong>For Development/Testing:</strong> Your reset code is: <strong>{{ session('verification_code') }}</strong>
        </div>
    @endif

    <p style="color: #666; margin-bottom: 1.5rem; text-align: center;">
        We've sent a 6-digit reset code to<br>
        <strong>{{ session('reset_email') }}</strong>
    </p>

    <form action="{{ route('student.reset.verify.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="code" class="form-label">Enter Reset Code</label>
            <input type="text" id="code" name="code" class="form-input" maxlength="6" placeholder="000000" required autofocus style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
            @error('code')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Verify Code</button>
    </form>

    <div class="text-center mt-3">
        <p><a href="{{ route('student.forgot-password') }}" class="link">Back to Forgot Password</a></p>
    </div>
</div>

<script>
// Auto-format code input
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
