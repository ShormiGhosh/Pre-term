@extends('layouts.app')

@section('title', 'Teacher Signup')

@section('content')
<div class="card">
    <h1 class="card-title">Teacher Signup</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('teacher.signup.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email (Format: teachername@dept.kuet.ac.bd)</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="example: johndoe@cse.kuet.ac.bd" required>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="department" class="form-label">Department</label>
            <input type="text" id="department" name="department" class="form-input" value="{{ old('department') }}" placeholder="e.g., CSE, EEE, ME" required>
            @error('department')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="designation" class="form-label">Designation (Optional)</label>
            <input type="text" id="designation" name="designation" class="form-input" value="{{ old('designation') }}" placeholder="e.g., Professor, Associate Professor">
            @error('designation')
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

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
        </div>

        <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>

    <div class="text-center mt-3">
        <p>Already have an account? <a href="{{ route('teacher.login') }}" class="link">Login here</a></p>
        <p><a href="{{ route('home') }}" class="link">Back to Home</a></p>
    </div>
</div>
@endsection
