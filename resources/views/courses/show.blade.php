@extends('layouts.app')

@section('title', $course->course_code . ' - ' . $course->course_title)

@section('content')
<style>
    .course-detail-container {
        display: flex;
        min-height: calc(100vh - 60px);
        position: relative;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 280px;
        background: linear-gradient(135deg, rgba(38, 41, 54, 0.95), rgba(64, 26, 117, 0.3));
        border-right: 1px solid rgba(193, 206, 229, 0.2);
        backdrop-filter: blur(20px);
        transition: transform 0.3s ease;
        position: fixed;
        left: 0;
        top: 60px;
        height: calc(100vh - 60px);
        z-index: 100;
        overflow-y: auto;
    }

    .sidebar.closed {
        transform: translateX(-280px);
    }

    .sidebar-header {
        padding: 2rem 1.5rem 1.5rem;
        border-bottom: 1px solid rgba(193, 206, 229, 0.2);
    }

    .sidebar-course-code {
        font-size: 1.25rem;
        font-weight: 600;
        color: #F1F5FB;
        margin-bottom: 0.5rem;
    }

    .sidebar-course-title {
        font-size: 0.875rem;
        color: #C1CEE5;
        line-height: 1.4;
    }

    .sidebar-menu {
        padding: 1.5rem 0;
    }

    .sidebar-menu-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        color: #C1CEE5;
        text-decoration: none;
        transition: all 0.3s;
        cursor: pointer;
        border-left: 3px solid transparent;
    }

    .sidebar-menu-item:hover {
        background: rgba(193, 206, 229, 0.1);
        color: #F1F5FB;
        border-left-color: #401a75;
    }

    .sidebar-menu-item.active {
        background: rgba(64, 26, 117, 0.3);
        color: #F1F5FB;
        border-left-color: #401a75;
    }

    .sidebar-menu-icon {
        font-size: 1.25rem;
        width: 24px;
        text-align: center;
    }

    .sidebar-menu-text {
        font-size: 0.9375rem;
        font-weight: 500;
    }

    /* Toggle Button */
    .sidebar-toggle {
        position: fixed;
        left: 280px;
        top: 80px;
        background: linear-gradient(135deg, #401a75, #5e2a9e);
        border: none;
        color: #F1F5FB;
        width: 40px;
        height: 40px;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: left 0.3s ease, transform 0.2s;
        z-index: 101;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
    }

    .sidebar-toggle:hover {
        transform: scale(1.05);
    }

    .sidebar-toggle.closed {
        left: 0;
        border-radius: 0 8px 8px 0;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 2rem;
        transition: margin-left 0.3s ease;
        min-height: calc(100vh - 60px);
    }

    .main-content.expanded {
        margin-left: 0;
    }

    .content-header {
        margin-bottom: 2rem;
    }

    .content-title {
        font-size: 2rem;
        color: #F1F5FB;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .content-subtitle {
        color: #8894AC;
        font-size: 1rem;
    }

    .content-section {
        background: linear-gradient(135deg, rgba(38, 41, 54, 0.5), rgba(64, 26, 117, 0.2));
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 12px;
        padding: 2rem;
        backdrop-filter: blur(10px);
    }

    .section-title {
        font-size: 1.5rem;
        color: #F1F5FB;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        font-size: 1.75rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .info-item {
        background: rgba(193, 206, 229, 0.05);
        padding: 1.25rem;
        border-radius: 8px;
        border: 1px solid rgba(193, 206, 229, 0.1);
    }

    .info-label {
        font-size: 0.75rem;
        color: #8894AC;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.125rem;
        color: #F1F5FB;
        font-weight: 600;
    }

    /* Placeholder content for sections */
    .placeholder-content {
        display: none;
    }

    .placeholder-content.active {
        display: block;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #8894AC;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-state-text {
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            max-width: 280px;
        }

        .sidebar.closed {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
        }

        .sidebar-toggle {
            left: 0;
        }

        .sidebar-toggle.closed {
            left: 0;
        }
    }
</style>

<div class="course-detail-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-course-code">{{ $course->course_code }}</div>
            <div class="sidebar-course-title">{{ $course->course_title }}</div>
        </div>
        <nav class="sidebar-menu">
            <a class="sidebar-menu-item active" data-section="overview">
                <span class="sidebar-menu-icon">📊</span>
                <span class="sidebar-menu-text">Overview</span>
            </a>
            <a class="sidebar-menu-item" data-section="attendance">
                <span class="sidebar-menu-icon">📋</span>
                <span class="sidebar-menu-text">Attendance Sheet</span>
            </a>
            <a class="sidebar-menu-item" data-section="ct-marks">
                <span class="sidebar-menu-icon">📝</span>
                <span class="sidebar-menu-text">CT Marks</span>
            </a>
            <a class="sidebar-menu-item" data-section="ct-schedule">
                <span class="sidebar-menu-icon">📅</span>
                <span class="sidebar-menu-text">CT Schedule</span>
            </a>
        </nav>
    </aside>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        ‹
    </button>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Overview Section -->
        <div class="placeholder-content active" id="section-overview">
            <div class="content-header">
                <h1 class="content-title">{{ $course->course_code }}</h1>
                <p class="content-subtitle">{{ $course->course_title }}</p>
            </div>

            <div class="content-section">
                <h2 class="section-title">
                    <span class="section-icon">ℹ️</span>
                    Course Information
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Course Code</div>
                        <div class="info-value">{{ $course->course_code }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Course Title</div>
                        <div class="info-value">{{ $course->course_title }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Credits</div>
                        <div class="info-value">{{ $course->course_credit }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Instructor</div>
                        <div class="info-value">{{ $course->teacher->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $course->department }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Enrolled Students</div>
                        <div class="info-value">{{ $course->students->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Sheet Section -->
        <div class="placeholder-content" id="section-attendance">
            <div class="content-header">
                <h1 class="content-title">Attendance Sheet</h1>
                <p class="content-subtitle">{{ $course->course_code }} - {{ $course->course_title }}</p>
            </div>

            <div class="content-section">
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <p class="empty-state-text">Attendance tracking will be available here</p>
                </div>
            </div>
        </div>

        <!-- CT Marks Section -->
        <div class="placeholder-content" id="section-ct-marks">
            <div class="content-header">
                <h1 class="content-title">CT Marks</h1>
                <p class="content-subtitle">{{ $course->course_code }} - {{ $course->course_title }}</p>
            </div>

            <div class="content-section">
                <div class="empty-state">
                    <div class="empty-state-icon">📝</div>
                    <p class="empty-state-text">CT marks will be displayed here</p>
                </div>
            </div>
        </div>

        <!-- CT Schedule Section -->
        <div class="placeholder-content" id="section-ct-schedule">
            <div class="content-header">
                <h1 class="content-title">CT Schedule</h1>
                <p class="content-subtitle">{{ $course->course_code }} - {{ $course->course_title }}</p>
            </div>

            <div class="content-section">
                <div class="empty-state">
                    <div class="empty-state-icon">📅</div>
                    <p class="empty-state-text">CT schedule will be shown here</p>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Sidebar Toggle
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mainContent = document.getElementById('mainContent');

sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('closed');
    sidebarToggle.classList.toggle('closed');
    mainContent.classList.toggle('expanded');
    
    // Change arrow direction
    if (sidebar.classList.contains('closed')) {
        sidebarToggle.textContent = '›';
    } else {
        sidebarToggle.textContent = '‹';
    }
});

// Section Navigation
const menuItems = document.querySelectorAll('.sidebar-menu-item');
const sections = document.querySelectorAll('.placeholder-content');

menuItems.forEach(item => {
    item.addEventListener('click', function() {
        // Remove active class from all items
        menuItems.forEach(mi => mi.classList.remove('active'));
        
        // Add active class to clicked item
        this.classList.add('active');
        
        // Hide all sections
        sections.forEach(section => section.classList.remove('active'));
        
        // Show selected section
        const sectionId = 'section-' + this.getAttribute('data-section');
        document.getElementById(sectionId).classList.add('active');
    });
});
</script>
@endsection
