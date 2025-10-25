@extends('layouts.app')

@section('title', 'Teacher Dashboard')

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
    }

    .add-course-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(64, 26, 117, 0.3);
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

    .course-card-clickable {
        text-decoration: none;
        color: inherit;
        display: block;
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

    <div class="dashboard-header">
        <h1 class="dashboard-title">My Courses</h1>
        <button class="add-course-btn" onclick="openAddCourseModal()">
            <span style="font-size: 1.5rem; line-height: 1;">+</span>
            Add Course
        </button>
    </div>

    <div class="courses-grid">
        @forelse(auth()->guard('teacher')->user()->courses as $course)
            <div class="course-card" onclick="window.location.href='{{ route('courses.show', $course->id) }}'" style="background: linear-gradient(135deg, 
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
                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this course?')">Delete Course</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="course-meta">
                    <div class="course-meta-item">
                        <span class="course-meta-label">Students</span>
                        <span class="course-meta-value">{{ $course->students->count() }}</span>
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
                <p>No courses created yet</p>
                <p style="font-size: 0.875rem; margin-top: 0.5rem;">Click "Add Course" to get started</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Course Modal -->
<div id="addCourseModal" class="modal-overlay" onclick="if(event.target === this) closeAddCourseModal()">
    <div class="modal-content">
        <h2 class="modal-title">Add New Course</h2>
        <form action="{{ route('courses.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Course Code</label>
                <input type="text" name="course_code" class="form-input" placeholder="e.g., CSE 4101" required>
            </div>
            <div class="form-group">
                <label class="form-label">Course Title</label>
                <input type="text" name="course_title" class="form-input" placeholder="e.g., Database Management Systems" required>
            </div>
            <div class="form-group">
                <label class="form-label">Course Credit</label>
                <input type="number" step="0.5" name="course_credit" class="form-input" placeholder="e.g., 3.0" required>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="closeAddCourseModal()">Cancel</button>
                <button type="submit" class="btn-submit">Add Course</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddCourseModal() {
    document.getElementById('addCourseModal').classList.add('show');
}

function closeAddCourseModal() {
    document.getElementById('addCourseModal').classList.remove('show');
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
</script>
@endsection

