@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="card">
    <h1 class="card-title">Forgot Password</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <p style="color: #666; margin-bottom: 1.5rem; text-align: center;">
        Enter your email address and we'll send you a code to reset your password.
    </p>

    <form action="{{ route('teacher.reset.send') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Send Reset Code</button>
    </form>

    <div class="text-center mt-3">
        <p><a href="{{ route('teacher.login') }}" class="link">Back to Login</a></p>
    </div>
</div>
@endsection
