@extends('layouts.app')

@section('title', 'Export to Google Calendar')

@section('content')
<style>
    .calendar-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
    }

    .calendar-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .calendar-title {
        font-size: 2rem;
        color: #F1F5FB;
        margin-bottom: 0.5rem;
    }

    .calendar-subtitle {
        color: #C1CEE5;
        font-size: 1rem;
    }

    .calendar-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .calendar-item {
        background: linear-gradient(135deg, rgba(64, 26, 117, 0.2), rgba(94, 42, 158, 0.1));
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .calendar-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(64, 26, 117, 0.3);
    }

    .calendar-info {
        flex: 1;
    }

    .ct-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #F1F5FB;
        margin-bottom: 0.5rem;
    }

    .ct-course {
        color: #C1CEE5;
        margin-bottom: 0.25rem;
    }

    .ct-datetime {
        color: #8B9DC3;
        font-size: 0.9rem;
    }

    .add-to-calendar-btn {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #401a75, #5e2a9e);
        color: #F1F5FB;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .add-to-calendar-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(64, 26, 117, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #8B9DC3;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #C1CEE5;
        text-decoration: none;
        margin-bottom: 2rem;
        transition: color 0.2s;
    }

    .back-btn:hover {
        color: #F1F5FB;
    }
</style>

<div class="calendar-container">
    <a href="{{ route('student.dashboard') }}" class="back-btn">
        <span class="material-symbols-outlined">arrow_back</span>
        Back to Dashboard
    </a>

    <div class="calendar-header">
        <h1 class="calendar-title">📅 Export to Google Calendar</h1>
        <p class="calendar-subtitle">Add your upcoming CT schedules to Google Calendar</p>
    </div>

    @if($calendarLinks->count() > 0)
        <div class="calendar-list">
            @foreach($calendarLinks as $item)
                <div class="calendar-item">
                    <div class="calendar-info">
                        <div class="ct-title">{{ $item['ct_name'] }}</div>
                        <div class="ct-course">{{ $item['course'] }}</div>
                        <div class="ct-datetime">
                            📆 {{ $item['datetime']->format('l, F j, Y') }} at {{ $item['datetime']->format('g:i A') }}
                        </div>
                    </div>
                    <a href="{{ $item['google_url'] }}" target="_blank" class="add-to-calendar-btn">
                        <span class="material-symbols-outlined">add</span>
                        Add to Google Calendar
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h3 style="color: #C1CEE5; margin-bottom: 0.5rem;">No Upcoming CTs</h3>
            <p>You don't have any upcoming CT schedules to export.</p>
        </div>
    @endif
</div>
@endsection
