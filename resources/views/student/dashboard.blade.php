@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .dashboard-title {
        font-size: 1.5rem;
        color: #F1F5FB;
        font-weight: 600;
    }

    .add-course-btn {
        position: fixed;
        top: 5rem;
        right: 1.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: linear-gradient(135deg, #401a75, #5e2a9e);
        color: #F1F5FB;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        z-index: 100;
    }

    .add-course-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(64, 26, 117, 0.3);
    }

    .notification-bell-btn {
        position: fixed;
        top: 5rem;
        right: 15.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5rem;
        height: 3.5rem;
        background: linear-gradient(135deg, #401a75, #5e2a9e);
        color: #F1F5FB;
        border: none;
        border-radius: 8px;
        font-size: 1.5rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        z-index: 100;
    }

    .notification-bell-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(64, 26, 117, 0.3);
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #F9896B;
        color: #fff;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }

    .notification-dropdown {
        position: fixed;
        top: 9rem;
        right: 15.5rem;
        width: 400px;
        max-height: 500px;
        background: #1c1a36;
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        display: none;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
    }

    .notification-dropdown.show {
        display: flex;
    }

    .notification-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(193, 206, 229, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-header h3 {
        font-size: 1.125rem;
        color: #F1F5FB;
        margin: 0;
    }

    .mark-all-read {
        background: transparent;
        color: #C1CEE5;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .mark-all-read:hover {
        background: rgba(193, 206, 229, 0.1);
        color: #F1F5FB;
    }

    .notification-list {
        overflow-y: auto;
        max-height: 400px;
    }

    .notification-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(193, 206, 229, 0.1);
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        gap: 1rem;
    }

    .notification-item:hover {
        background: rgba(64, 26, 117, 0.1);
    }

    .notification-item.unread {
        background: rgba(64, 26, 117, 0.15);
    }

    .notification-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #F1F5FB;
        margin-bottom: 0.25rem;
    }

    .notification-message {
        font-size: 0.85rem;
        color: #C1CEE5;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #888;
    }

    .notification-empty {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #888;
    }

    .notification-empty .material-symbols-outlined {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .notification-empty p {
        margin: 0;
        font-size: 0.9rem;
    }

    .courses-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .course-card {
        background: linear-gradient(135deg, rgba(64, 26, 117, 0.2), rgba(94, 42, 158, 0.1));
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
        backdrop-filter: blur(10px);
        min-width: 320px;
        max-width: 320px;
        cursor: pointer;
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(64, 26, 117, 0.2);
    }

    .course-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .course-code {
        font-size: 1.25rem;
        font-weight: 600;
        color: #F1F5FB;
        margin-bottom: 0.25rem;
    }

    .course-title {
        font-size: 1rem;
        color: #C1CEE5;
        margin-bottom: 0.75rem;
    }

    .course-meta {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(193, 206, 229, 0.2);
    }

    .last-visited-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.25);
    }

    .course-meta-item {
        display: flex;
        flex-direction: column;
    }

    .course-meta-label {
        font-size: 0.75rem;
        color: #8894AC;
        text-transform: uppercase;
    }

    .course-meta-value {
        font-size: 0.875rem;
        color: #F1F5FB;
        font-weight: 500;
    }

    .three-dot-menu {
        position: relative;
    }

    .three-dot-btn {
        background: none;
        border: none;
        color: #C1CEE5;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        font-size: 1.25rem;
        line-height: 1;
    }

    .three-dot-btn:hover {
        color: #F1F5FB;
    }

    .dropdown-menu-custom {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background: rgba(38, 41, 54, 0.95);
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 8px;
        padding: 0.5rem 0;
        min-width: 140px;
        backdrop-filter: blur(10px);
        z-index: 1000;
    }

    .dropdown-menu-custom.show {
        display: block;
    }

    .dropdown-menu-custom button {
        width: 100%;
        padding: 0.625rem 1rem;
        background: none;
        border: none;
        color: #F1F5FB;
        text-align: left;
        cursor: pointer;
        transition: background 0.2s;
    }

    .dropdown-menu-custom button:hover {
        background: rgba(193, 206, 229, 0.1);
    }

    .subject-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 0.75rem;
        opacity: 0.7;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: linear-gradient(135deg, rgba(38, 41, 54, 0.95), rgba(64, 26, 117, 0.3));
        border: 1px solid rgba(193, 206, 229, 0.3);
        border-radius: 12px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        backdrop-filter: blur(20px);
    }

    .modal-title {
        font-size: 1.5rem;
        color: #F1F5FB;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        color: #C1CEE5;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        background: rgba(193, 206, 229, 0.1);
        border: 1px solid rgba(193, 206, 229, 0.3);
        border-radius: 8px;
        color: #F1F5FB;
        font-size: 1rem;
    }

    .form-input:focus {
        outline: none;
        border-color: #401a75;
        background: rgba(193, 206, 229, 0.15);
    }

    .modal-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-cancel {
        flex: 1;
        padding: 0.75rem;
        background: rgba(193, 206, 229, 0.2);
        color: #F1F5FB;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-submit {
        flex: 1;
        padding: 0.75rem;
        background: linear-gradient(135deg, #401a75, #5e2a9e);
        color: #F1F5FB;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #8894AC;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        animation: slideIn 0.3s ease-out;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.2);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #4ade80;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<div class="dashboard-container">
    @if(session('success'))
        <div class="alert alert-success" id="successAlert">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" id="errorAlert">{{ session('error') }}</div>
    @endif

    <!-- Notification Bell - Top Right -->
    <button class="notification-bell-btn" id="notificationBell">
        <span class="material-symbols-outlined">notifications</span>
        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
    </button>

    <!-- Notification Dropdown -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="mark-all-read" id="markAllRead">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-empty">
                <span class="material-symbols-outlined">notifications_off</span>
                <p>No notifications yet</p>
            </div>
        </div>
    </div>

    <!-- Enroll Button - Top Right Corner -->
    <button class="add-course-btn" onclick="openEnrollModal()">
        <span style="font-size: 1.5rem; line-height: 1;">+</span>
        Enroll in Courses
    </button>

    <h1 class="dashboard-title" style="margin-bottom: 2rem;">My Courses</h1>

    {{-- Last Visited Course Section --}}
    @if(isset($lastCourse) && $lastCourse)
    <div class="last-visited-section" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(124, 58, 237, 0.1)); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 12px; padding: 1.25rem; margin-bottom: 2rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onclick="window.location.href='{{ route('student.courses.show', $lastCourse->id) }}'">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-size: 2rem;">🔖</span>
            <div style="flex: 1;">
                <div style="font-size: 0.875rem; color: #a78bfa; margin-bottom: 0.25rem; font-weight: 500;">
                    Continue Where You Left Off
                </div>
                <div style="font-size: 1.125rem; color: #F1F5FB; font-weight: 600;">
                    {{ $lastCourse->course_code }} - {{ $lastCourse->course_title }}
                </div>
                <div style="font-size: 0.875rem; color: #C1CEE5; margin-top: 0.25rem;">
                    {{ $lastCourse->teacher->name }}
                </div>
            </div>
            <span style="font-size: 1.5rem; color: #a78bfa;">→</span>
        </div>
    </div>
    @endif

    <div class="courses-grid">
        @forelse(auth()->guard('student')->user()->courses as $course)
            <div class="course-card" onclick="window.location.href='{{ route('student.courses.show', $course->id) }}'" style="background: linear-gradient(135deg, 
                @if(str_contains($course->course_code, 'CSE') || str_contains($course->course_code, 'EEE'))
                    rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.1)
                @elseif(str_contains($course->course_code, 'ME') || str_contains($course->course_code, 'IPE'))
                    rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.1)
                @elseif(str_contains($course->course_code, 'CE') || str_contains($course->course_code, 'URP'))
                    rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.1)
                @else
                    rgba(168, 85, 247, 0.2), rgba(147, 51, 234, 0.1)
                @endif
            );">
                <div class="course-header">
                    <div>
                        <svg class="subject-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="course-code">{{ $course->course_code }}</div>
                        <div class="course-title">{{ $course->course_title }}</div>
                    </div>
                    <div class="three-dot-menu">
                        <button class="three-dot-btn" onclick="event.stopPropagation(); toggleDropdown({{ $course->id }})">⋮</button>
                        <div id="dropdown-{{ $course->id }}" class="dropdown-menu-custom">
                            <form action="{{ route('courses.unenroll', $course->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to unenroll from this course?')">Unenroll</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="course-meta">
                    <div class="course-meta-item">
                        <span class="course-meta-label">Instructor</span>
                        <span class="course-meta-value">{{ $course->teacher->name }}</span>
                    </div>
                    <div class="course-meta-item">
                        <span class="course-meta-label">Credit</span>
                        <span class="course-meta-value">{{ $course->course_credit }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <p>No courses enrolled yet</p>
                <p style="font-size: 0.875rem; margin-top: 0.5rem;">Click "Enroll in Course" to get started</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Enrollment Modal -->
<div id="enrollModal" class="modal-overlay" onclick="if(event.target === this) closeEnrollModal()">
    <div class="modal-content" style="max-width: 600px; max-height: 80vh; overflow-y: auto;">
        <h2 class="modal-title">Enroll in Courses</h2>
        <form action="{{ route('courses.enroll') }}" method="POST">
            @csrf
            <div class="form-group">
                @php
                    $allCourses = \App\Models\Course::with('teacher')->get();
                    $enrolledCourseIds = auth()->guard('student')->user()->courses->pluck('id')->toArray();
                    $availableCourses = $allCourses->whereNotIn('id', $enrolledCourseIds);
                @endphp
                
                @if($availableCourses->count() > 0)
                    <label class="form-label" style="margin-bottom: 1rem;">Select courses to enroll:</label>
                    <div style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
                        @foreach($availableCourses as $course)
                            <label class="course-checkbox-item">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" class="course-checkbox">
                                <div class="course-checkbox-content">
                                    <div class="course-checkbox-code">{{ $course->course_code }}</div>
                                    <div class="course-checkbox-title">{{ $course->course_title }}</div>
                                    <div class="course-checkbox-teacher">Instructor: {{ $course->teacher->name }}</div>
                                    <div class="course-checkbox-credit">Credit: {{ $course->course_credit }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p style="color: #8894AC; text-align: center; padding: 2rem;">
                        No available courses to enroll. You are either enrolled in all courses or no courses have been created yet.
                    </p>
                @endif
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="closeEnrollModal()">Cancel</button>
                @if($availableCourses->count() > 0)
                    <button type="submit" class="btn-submit">Enroll in Selected</button>
                @endif
            </div>
        </form>
    </div>
</div>

<style>
    .course-checkbox-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: rgba(193, 206, 229, 0.05);
        border: 2px solid rgba(193, 206, 229, 0.2);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .course-checkbox-item:hover {
        background: rgba(193, 206, 229, 0.1);
        border-color: rgba(193, 206, 229, 0.3);
    }

    .course-checkbox-item:has(.course-checkbox:checked) {
        background: rgba(64, 26, 117, 0.2);
        border-color: #401a75;
    }

    .course-checkbox {
        width: 20px;
        height: 20px;
        margin-top: 0.25rem;
        cursor: pointer;
        accent-color: #401a75;
    }

    .course-checkbox-content {
        flex: 1;
    }

    .course-checkbox-code {
        font-size: 1rem;
        font-weight: 600;
        color: #F1F5FB;
        margin-bottom: 0.25rem;
    }

    .course-checkbox-title {
        font-size: 0.875rem;
        color: #C1CEE5;
        margin-bottom: 0.5rem;
    }

    .course-checkbox-teacher {
        font-size: 0.75rem;
        color: #8894AC;
        margin-bottom: 0.25rem;
    }

    .course-checkbox-credit {
        font-size: 0.75rem;
        color: #8894AC;
    }
</style>

<script>
function openEnrollModal() {
    document.getElementById('enrollModal').classList.add('show');
}

function closeEnrollModal() {
    document.getElementById('enrollModal').classList.remove('show');
}

function toggleDropdown(courseId) {
    const dropdown = document.getElementById('dropdown-' + courseId);
    const allDropdowns = document.querySelectorAll('.dropdown-menu-custom');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d !== dropdown) {
            d.classList.remove('show');
        }
    });
    
    dropdown.classList.toggle('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.three-dot-menu')) {
        document.querySelectorAll('.dropdown-menu-custom').forEach(d => {
            d.classList.remove('show');
        });
    }
});

// Auto-dismiss alerts after 3 seconds
setTimeout(function() {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    
    if (successAlert) {
        successAlert.style.transition = 'opacity 0.5s';
        successAlert.style.opacity = '0';
        setTimeout(() => successAlert.remove(), 500);
    }
    
    if (errorAlert) {
        errorAlert.style.transition = 'opacity 0.5s';
        errorAlert.style.opacity = '0';
        setTimeout(() => errorAlert.remove(), 500);
    }
}, 3000);

// Notification system JavaScript
let notificationsData = [];

// Toggle notification dropdown
document.getElementById('notificationBell').addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
    
    if (dropdown.classList.contains('show')) {
        loadNotifications();
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notificationDropdown');
    const bell = document.getElementById('notificationBell');
    
    if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// Load notifications
function loadNotifications() {
    fetch('/notifications', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notificationsData = data.notifications.data;
            displayNotifications(notificationsData);
        }
    })
    .catch(error => console.error('Error loading notifications:', error));
}

// Display notifications in dropdown
function displayNotifications(notifications) {
    const listContainer = document.getElementById('notificationList');
    
    if (notifications.length === 0) {
        listContainer.innerHTML = `
            <div class="notification-empty">
                <span class="material-symbols-outlined">notifications_off</span>
                <p>No notifications yet</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = notifications.map(notif => `
        <div class="notification-item ${notif.is_read ? '' : 'unread'}" 
             data-id="${notif.id}" 
             onclick="handleNotificationClick(${notif.id}, '${notif.link || '#'}')">
            <div class="notification-content">
                <div class="notification-title">${notif.title}</div>
                <div class="notification-message">${notif.message}</div>
                <div class="notification-time">${formatTimeAgo(notif.created_at)}</div>
            </div>
        </div>
    `).join('');
}

// Handle notification click
function handleNotificationClick(notificationId, link) {
    // Mark as read
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateUnreadCount();
            
            // Navigate to link if provided
            if (link && link !== '#') {
                window.location.href = link;
            } else {
                // Just reload notifications
                loadNotifications();
            }
        }
    });
}

// Mark all as read
document.getElementById('markAllRead').addEventListener('click', function(e) {
    e.stopPropagation();
    
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateUnreadCount();
            loadNotifications();
        }
    });
});

// Update unread count badge
function updateUnreadCount() {
    fetch('/notifications/unread-count', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('notificationBadge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
    });
}

// Format time ago
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' min ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
    return date.toLocaleDateString();
}

// Initial load and periodic refresh
updateUnreadCount();
setInterval(updateUnreadCount, 30000); // Update every 30 seconds
</script>
@endsection

