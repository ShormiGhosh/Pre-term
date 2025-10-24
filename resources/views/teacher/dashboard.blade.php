@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="card" style="max-width: 800px;">
    <h1 class="card-title">Teacher Dashboard</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="dashboard-card">
        <h2 style="color: #C1CEE5; margin-bottom: 1rem;">Welcome, {{ session('user_name') }}!</h2>
        
        <div class="user-info">
            <p><strong>Email:</strong> {{ session('user_email') }}</p>
            <p><strong>Role:</strong> Teacher</p>
            <p><strong>User ID:</strong> {{ session('user_id') }}</p>
        </div>

        <p style="color: #F1F5FB; margin-top: 1rem;">
            This is your dashboard where you can:
        </p>
        
        <ul style="margin: 1rem 0; padding-left: 2rem; color: #C1CEE5;">
            <li>Manage CT schedules</li>
            <li>Record CT marks</li>
            <li>Track student attendance</li>
            <li>Manage class performance records</li>
            <li>View student attendance status</li>
        </ul>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <a href="{{ route('teacher.profile') }}" 
           style="flex: 1; padding: 0.875rem; background: #401a75; color: #F1F5FB; border: none; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; text-align: center; text-decoration: none; display: inline-block;">
            View Profile
        </a>
        
        <form action="{{ route('teacher.logout') }}" method="POST" style="flex: 1;">
            @csrf
            <button type="submit" class="btn btn-danger" style="width: 100%;">
                Logout
            </button>
        </form>
    </div>
</div>
@endsection

