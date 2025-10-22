@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="card" style="max-width: 800px;">
    <h1 class="card-title">Teacher Dashboard</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="dashboard-card">
        <h2 style="color: #667eea; margin-bottom: 1rem;">Welcome, {{ session('user_name') }}!</h2>
        
        <div class="user-info">
            <p><strong>Email:</strong> {{ session('user_email') }}</p>
            <p><strong>Role:</strong> Teacher</p>
            <p><strong>User ID:</strong> {{ session('user_id') }}</p>
        </div>

        <p style="color: #666; margin-top: 1rem;">
            You are successfully logged in as a teacher. This is your dashboard where you will be able to:
        </p>
        
        <ul style="margin: 1rem 0; padding-left: 2rem; color: #666;">
            <li>Manage CT schedules</li>
            <li>Record CT marks</li>
            <li>Track student attendance</li>
            <li>Manage class performance records</li>
            <li>View student attendance status</li>
        </ul>

        <p style="color: #888; font-style: italic;">
            More features will be added soon!
        </p>
    </div>

    <form action="{{ route('teacher.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
</div>
@endsection
