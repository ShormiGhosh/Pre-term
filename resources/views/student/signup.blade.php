@extends('layouts.app')

@section('title', 'Student Signup')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="card">
    <h1 class="card-title">Student Signup</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('student.signup.submit') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email (Format: surnameRoll@stud.kuet.ac.bd)</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="example: smith2103001@stud.kuet.ac.bd" required>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="roll_number" class="form-label">Roll Number</label>
            <input type="text" id="roll_number" name="roll_number" class="form-input" value="{{ old('roll_number') }}" placeholder="e.g., 2103001" required>
            @error('roll_number')
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
            <label for="year" class="form-label">Year</label>
            <select id="year" name="year" class="form-input" required>
                <option value="">Select Year</option>
                <option value="1" {{ old('year') == '1' ? 'selected' : '' }}>1st Year</option>
                <option value="2" {{ old('year') == '2' ? 'selected' : '' }}>2nd Year</option>
                <option value="3" {{ old('year') == '3' ? 'selected' : '' }}>3rd Year</option>
                <option value="4" {{ old('year') == '4' ? 'selected' : '' }}>4th Year</option>
            </select>
            @error('year')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="semester" class="form-label">Semester</label>
            <select id="semester" name="semester" class="form-input" required>
                <option value="">Select Semester</option>
                <option value="1st" {{ old('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                <option value="2nd" {{ old('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
            </select>
            @error('semester')
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

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div style="position: relative;">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" style="padding-right: 45px;" required>
                <button type="button" onclick="togglePassword('password_confirmation', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #C1CEE5; padding: 0; display: flex; align-items: center;">
                    <span class="material-icons" style="font-size: 20px;">visibility_off</span>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>

    <div class="text-center mt-3">
        <p>Already have an account? <a href="{{ route('student.login') }}" class="link">Login here</a></p>
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

