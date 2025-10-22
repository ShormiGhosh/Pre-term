@extends('layouts.app')

@section('title', 'Teacher Login')

@section('content')
<div class="card">
    <h1 class="card-title">Teacher Login</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('teacher.login.submit') }}" method="POST">
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
            <input type="password" id="password" name="password" class="form-input" required>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="text-center mt-3">
        <p>Don't have an account? <a href="{{ route('teacher.signup') }}" class="link">Sign up here</a></p>
        <p><a href="{{ route('home') }}" class="link">Back to Home</a></p>
    </div>
</div>
@endsection
