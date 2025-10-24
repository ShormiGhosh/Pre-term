@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div style="min-height: calc(100vh - 64px); background: #100f21; padding: 2rem;">
    
    @if(session('success'))
        <div style="background: #401a75; color: #F1F5FB; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #C1CEE5;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Profile Card -->
    <div style="background: #1c1a36; border-radius: 12px; padding: 3rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);">
        
        <!-- Profile Header with Image -->
        <div style="display: flex; align-items: center; gap: 3rem; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 2px solid #302e4a;">
            <!-- Profile Image -->
            <div style="flex-shrink: 0;">
                @if($student->profile_image)
                    <img src="{{ asset('uploads/profiles/' . $student->profile_image) }}" 
                         alt="Profile Image" 
                         style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #401a75; box-shadow: 0 4px 12px rgba(64, 26, 117, 0.5);">
                @else
                    <div style="width: 150px; height: 150px; border-radius: 50%; background: #302e4a; display: flex; align-items: center; justify-content: center; border: 4px solid #401a75; box-shadow: 0 4px 12px rgba(64, 26, 117, 0.5);">
                        <span class="material-symbols-outlined" style="font-size: 80px; color: #C1CEE5;">person</span>
                    </div>
                @endif
            </div>

            <!-- Name and Role -->
            <div style="flex: 1;">
                <h1 style="color: #F1F5FB; font-size: 2.5rem; margin: 0 0 0.5rem 0; font-weight: 600;">{{ $student->name }}</h1>
                <p style="color: #C1CEE5; font-size: 1.2rem; margin: 0;">Student</p>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="{{ route('student.profile.edit') }}" 
                   style="padding: 0.875rem 2rem; background: #401a75; color: #F1F5FB; border: none; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.3s;">
                    Edit Profile
                </a>
                
                <form action="{{ route('student.profile.delete') }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone!');" 
                      style="display: inline-block;">
                    @csrf
                    <button type="submit" 
                            style="padding: 0.875rem 2rem; background: #2d1a1f; color: #F9896B; border: 1px solid #F9896B; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background 0.3s;">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>

        <!-- Profile Information Grid -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
            
            <!-- Email -->
            <div style="background: #302e4a; padding: 1.5rem; border-radius: 8px;">
                <p style="color: #C1CEE5; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</p>
                <p style="color: #F1F5FB; font-size: 1.1rem; margin: 0; word-break: break-word;">{{ $student->email }}</p>
            </div>

            <!-- Roll Number -->
            <div style="background: #302e4a; padding: 1.5rem; border-radius: 8px;">
                <p style="color: #C1CEE5; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.5px;">Roll Number</p>
                <p style="color: #F1F5FB; font-size: 1.1rem; margin: 0;">{{ $student->roll_number }}</p>
            </div>

            <!-- Department -->
            <div style="background: #302e4a; padding: 1.5rem; border-radius: 8px;">
                <p style="color: #C1CEE5; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.5px;">Department</p>
                <p style="color: #F1F5FB; font-size: 1.1rem; margin: 0;">{{ $student->department }}</p>
            </div>

            <!-- Year -->
            <div style="background: #302e4a; padding: 1.5rem; border-radius: 8px;">
                <p style="color: #C1CEE5; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.5px;">Year</p>
                <p style="color: #F1F5FB; font-size: 1.1rem; margin: 0;">Year {{ $student->year }}</p>
            </div>

            <!-- Semester -->
            <div style="background: #302e4a; padding: 1.5rem; border-radius: 8px;">
                <p style="color: #C1CEE5; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.5px;">Semester</p>
                <p style="color: #F1F5FB; font-size: 1.1rem; margin: 0;">{{ $student->semester }}</p>
            </div>

            <!-- Email Verification Status -->
            <div style="background: #302e4a; padding: 1.5rem; border-radius: 8px;">
                <p style="color: #C1CEE5; font-size: 0.9rem; margin: 0 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.5px;">Email Status</p>
                <p style="color: {{ $student->email_verified ? '#4ade80' : '#F9896B' }}; font-size: 1.1rem; margin: 0; font-weight: 500;">
                    {{ $student->email_verified ? 'Verified' : 'Not Verified' }}
                </p>
            </div>

        </div>

    </div>
</div>

<style>
    a:hover, button:hover {
        opacity: 0.85;
    }
</style>
@endsection
