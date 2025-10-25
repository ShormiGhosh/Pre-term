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
        justify-content: flex-start;
        gap: 0.75rem;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: 0.75rem;
        vertical-align: middle;
    }

    .sidebar-menu-text {
        font-size: 0.9375rem;
        font-weight: 500;
        vertical-align: middle;
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
        display: none;
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
        display: none;
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

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
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
            <div class="sidebar-menu-item active" data-section="overview">
                <span class="sidebar-menu-icon material-symbols-outlined">overview</span>
                <span class="sidebar-menu-text">Overview</span>
            </div>
            <div class="sidebar-menu-item" data-section="attendance">
                <span class="sidebar-menu-icon material-symbols-outlined">monitoring</span>
                <span class="sidebar-menu-text">Attendance Sheet</span>
            </div>
            <div class="sidebar-menu-item" data-section="ct-marks">
                <span class="sidebar-menu-icon material-symbols-outlined">scoreboard</span>
                <span class="sidebar-menu-text">CT Marks</span>
            </div>
            <div class="sidebar-menu-item" data-section="ct-schedule">
                <span class="sidebar-menu-icon material-symbols-outlined">timer</span>
                <span class="sidebar-menu-text">CT Schedule</span>
            </div>
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
                @include('courses.attendance-section')
            </div>
        </div>

        @include('courses.ct-marks-section')

        <!-- CT Schedule Section -->
        <div class="placeholder-content" id="section-ct-schedule">
            <div class="content-header">
                <h1 class="content-title">CT Schedule</h1>
                <p class="content-subtitle">{{ $course->course_code }} - {{ $course->course_title }}</p>
            </div>

            @if($user instanceof \App\Models\Teacher && $user->id === $course->teacher_id)
            <!-- Teacher View: Add CT Schedule Form -->
            <div class="content-section" style="margin-bottom: 2rem;">
                <h2 class="section-title">Schedule New CT</h2>
                <form action="{{ route('ct-schedules.store', $course->id) }}" method="POST" class="ct-form">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="ct_name">CT Name *</label>
                            <input type="text" id="ct_name" name="ct_name" required 
                                   placeholder="e.g., CT 1, CT 2, Midterm" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="ct_datetime">Date & Time *</label>
                            <input type="datetime-local" id="ct_datetime" name="ct_datetime" 
                                   required class="form-input" min="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="total_marks">Total Marks *</label>
                            <input type="number" id="total_marks" name="total_marks" 
                                   required min="1" placeholder="e.g., 20" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3" 
                                  placeholder="Additional information about the CT..." class="form-input"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        Schedule CT & Notify Students
                    </button>
                </form>
            </div>
            @endif

            <!-- Scheduled CTs List -->
            <div class="content-section" id="scheduled-cts-section">
                <h2 class="section-title">Scheduled CTs</h2>

                @php
                    $upcomingCTs = $course->ctSchedules()->where('ct_datetime', '>', now())->orderBy('ct_datetime', 'asc')->get();
                    $pastCTs = $course->ctSchedules()->where('ct_datetime', '<=', now())->orderBy('ct_datetime', 'desc')->get();
                @endphp

                @if($upcomingCTs->count() > 0)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #F1F5FB; font-size: 1.25rem; margin-bottom: 1rem;">Upcoming CTs</h3>
                    <div class="ct-cards-grid" id="upcoming-cts">
                        @foreach($upcomingCTs as $ct)
                        <div class="ct-card upcoming-ct" data-ct-timestamp="{{ $ct->ct_datetime->timestamp * 1000 }}" data-ct-id="{{ $ct->id }}">
                            <div class="ct-card-header">
                                <div>
                                    <h3 class="ct-name">{{ $ct->ct_name }}</h3>
                                    <div class="ct-datetime">
                                        {{ $ct->ct_datetime->format('l, F j, Y') }}<br>
                                        {{ $ct->ct_datetime->format('g:i A') }}
                                    </div>
                                </div>
                                @if($user instanceof \App\Models\Teacher && $user->id === $course->teacher_id)
                                <form action="{{ route('ct-schedules.destroy', $ct->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" 
                                            onclick="return confirm('Are you sure you want to delete this CT schedule?')">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                            
                            <div class="ct-card-body">
                                <div class="ct-info-row">
                                    <span class="ct-label">Total Marks:</span>
                                    <span class="ct-value">{{ $ct->total_marks }}</span>
                                </div>
                                
                                @if($ct->description)
                                <div class="ct-description">
                                    <strong>Description:</strong>
                                    <p>{{ $ct->description }}</p>
                                </div>
                                @endif
                                
                                <!-- Countdown Timer for Students -->
                                @if($user instanceof \App\Models\Student)
                                <div class="countdown-timer" id="countdown-{{ $ct->id }}">
                                    <div class="countdown-label">Time Remaining:</div>
                                    <div class="countdown-display">
                                        <div class="countdown-part">
                                            <span class="countdown-number days">00</span>
                                            <span class="countdown-text">Days</span>
                                        </div>
                                        <div class="countdown-part">
                                            <span class="countdown-number hours">00</span>
                                            <span class="countdown-text">Hours</span>
                                        </div>
                                        <div class="countdown-part">
                                            <span class="countdown-number minutes">00</span>
                                            <span class="countdown-text">Minutes</span>
                                        </div>
                                        <div class="countdown-part">
                                            <span class="countdown-number seconds">00</span>
                                            <span class="countdown-text">Seconds</span>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <!-- Status badge for Teachers -->
                                <div class="countdown-timer" id="countdown-{{ $ct->id }}">
                                    <div style="text-align: center; padding: 0.75rem; background: rgba(59, 130, 246, 0.15); border: 2px solid rgba(59, 130, 246, 0.4); border-radius: 8px;">
                                        <span style="color: #93c5fd; font-weight: 700; font-size: 1rem;">⏰ Scheduled</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($pastCTs->count() > 0)
                <div>
                    <h3 style="color: #F1F5FB; font-size: 1.25rem; margin-bottom: 1rem;">Past CTs</h3>
                    <div class="ct-cards-grid" id="past-cts">
                        @foreach($pastCTs as $ct)
                        <div class="ct-card past-ct">
                            <div class="ct-card-header">
                                <div>
                                    <h3 class="ct-name">{{ $ct->ct_name }}</h3>
                                    <div class="ct-datetime">
                                        {{ $ct->ct_datetime->format('l, F j, Y') }}<br>
                                        {{ $ct->ct_datetime->format('g:i A') }}
                                    </div>
                                </div>
                                @if($user instanceof \App\Models\Teacher && $user->id === $course->teacher_id)
                                <form action="{{ route('ct-schedules.destroy', $ct->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" 
                                            onclick="return confirm('Are you sure you want to delete this CT schedule?')">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                            
                            <div class="ct-card-body">
                                <div class="ct-info-row">
                                    <span class="ct-label">Total Marks:</span>
                                    <span class="ct-value">{{ $ct->total_marks }}</span>
                                </div>
                                
                                @if($ct->description)
                                <div class="ct-description">
                                    <strong>Description:</strong>
                                    <p>{{ $ct->description }}</p>
                                </div>
                                @endif
                                
                                <div class="status-badge completed">
                                    Completed
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($upcomingCTs->count() === 0 && $pastCTs->count() === 0)
                <div class="empty-state">
                    <p class="empty-state-text">No CT schedules yet</p>
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

<style>
    /* CT Schedule Styles */
    .ct-form {
        margin-top: 1.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        color: #F1F5FB;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(193, 206, 229, 0.1);
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 8px;
        color: #F1F5FB;
        font-size: 0.9375rem;
        transition: all 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: #401a75;
        background: rgba(193, 206, 229, 0.15);
    }

    .form-input::placeholder {
        color: #8894AC;
    }

    .btn-submit {
        background: linear-gradient(135deg, #401a75, #5e2a9e);
        color: #F1F5FB;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(64, 26, 117, 0.4);
    }

    .ct-cards-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .ct-card {
        background: linear-gradient(135deg, rgba(38, 41, 54, 0.6), rgba(64, 26, 117, 0.3));
        border: 1px solid rgba(193, 206, 229, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        flex: 0 1 calc(50% - 0.75rem);
        min-width: 300px;
    }

    @media (max-width: 768px) {
        .ct-card {
            flex: 1 1 100%;
        }
    }

    .ct-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .upcoming-ct {
        border-left: 4px solid #10b981;
    }

    .past-ct {
        opacity: 0.7;
        border-left: 4px solid #6b7280;
    }

    .ct-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(193, 206, 229, 0.1);
    }

    .ct-name {
        color: #F1F5FB;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .ct-datetime {
        color: #8894AC;
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.4);
        color: #fca5a5;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.3s;
    }

    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.3);
    }

    .ct-card-body {
        color: #C1CEE5;
    }

    .ct-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .ct-label {
        color: #8894AC;
        font-size: 0.875rem;
    }

    .ct-value {
        color: #F1F5FB;
        font-weight: 600;
    }

    .ct-description {
        margin-top: 1rem;
        padding: 1rem;
        background: rgba(193, 206, 229, 0.05);
        border-radius: 8px;
        border-left: 3px solid #401a75;
    }

    .ct-description strong {
        color: #F1F5FB;
        font-size: 0.875rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .ct-description p {
        color: #C1CEE5;
        font-size: 0.875rem;
        margin: 0;
    }

    .countdown-timer {
        margin-top: 1.5rem;
        padding: 1.25rem;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 10px;
    }

    .countdown-label {
        color: #10b981;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 1rem;
        text-transform: uppercase;
    }

    .countdown-display {
        display: flex;
        justify-content: space-around;
        gap: 0.5rem;
    }

    .countdown-part {
        text-align: center;
        flex: 1;
    }

    .countdown-number {
        display: block;
        font-size: 1.75rem;
        font-weight: 700;
        color: #10b981;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .countdown-text {
        display: block;
        font-size: 0.75rem;
        color: #8894AC;
        text-transform: uppercase;
    }

    .status-badge {
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge.completed {
        background: rgba(107, 114, 128, 0.2);
        color: #9ca3af;
        border: 1px solid rgba(107, 114, 128, 0.3);
    }
</style>

<script>
// Script runs immediately
console.log('Script is running!');

// Sidebar Toggle
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mainContent = document.getElementById('mainContent');

console.log('Sidebar:', sidebar);
console.log('SidebarToggle:', sidebarToggle);
console.log('MainContent:', mainContent);

if (sidebar && sidebarToggle && mainContent) {
    sidebarToggle.addEventListener('click', function() {
        console.log('Sidebar toggle clicked!');
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
}

// Section Navigation
const menuItems = document.querySelectorAll('.sidebar-menu-item');
const sections = document.querySelectorAll('.placeholder-content');

console.log('Menu items found:', menuItems.length);
console.log('Sections found:', sections.length);

// Function to activate a section
function activateSection(sectionName) {
    // Remove active class from all items
    menuItems.forEach(mi => mi.classList.remove('active'));
    
    // Hide all sections
    sections.forEach(section => section.classList.remove('active'));
    
    // Find and activate the menu item and section
    const menuItem = document.querySelector(`.sidebar-menu-item[data-section="${sectionName}"]`);
    const targetSection = document.getElementById('section-' + sectionName);
    
    if (menuItem && targetSection) {
        menuItem.classList.add('active');
        targetSection.classList.add('active');
        console.log('Section activated:', sectionName);
    }
}

// Restore last active tab from localStorage
const courseId = {{ $course->id }};
const lastActiveTab = localStorage.getItem(`course_${courseId}_activeTab`);
if (lastActiveTab) {
    activateSection(lastActiveTab);
}

menuItems.forEach(item => {
    item.addEventListener('click', function() {
        const sectionName = this.getAttribute('data-section');
        console.log('Clicked on:', sectionName);
        
        // Activate the section
        activateSection(sectionName);
        
        // Save to localStorage
        localStorage.setItem(`course_${courseId}_activeTab`, sectionName);
    });
});

    // Countdown Timer for CTs
    function updateCountdowns() {
        const ctCards = document.querySelectorAll('.upcoming-ct');
        
        ctCards.forEach(card => {
            const ctTimestamp = card.getAttribute('data-ct-timestamp');
            
            if (!ctTimestamp) {
                return;
            }
            
            // Timestamp is already in milliseconds
            // Subtract 6 hours (6 * 60 * 60 * 1000 = 21600000 ms) for countdown calculation
            const adjustedTimestamp = parseInt(ctTimestamp) - (6 * 60 * 60 * 1000);
            const ctDatetime = new Date(adjustedTimestamp);
            const now = new Date();
            const diff = ctDatetime - now;
            
            if (diff <= 0) {
                // Countdown finished, show completed badge
                const countdown = card.querySelector('.countdown-timer');
                if (countdown) {
                    countdown.innerHTML = '<div style="text-align: center; padding: 0.75rem; background: rgba(34, 197, 94, 0.15); border: 2px solid rgba(34, 197, 94, 0.4); border-radius: 8px;"><span style="color: #86efac; font-weight: 700; font-size: 1rem;">✓ Completed</span></div>';
                }
                
                // Mark card as completed (for both teacher and student)
                if (!card.classList.contains('ct-completed')) {
                    card.classList.add('ct-completed');
                    card.style.opacity = '0.7';
                    card.style.border = '2px solid rgba(34, 197, 94, 0.3)';
                }
                
                return;
            }
            
            // Calculate time units
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            // Find countdown element within this card
            const countdown = card.querySelector('.countdown-timer');
            if (countdown) {
                const daysEl = countdown.querySelector('.days');
                const hoursEl = countdown.querySelector('.hours');
                const minutesEl = countdown.querySelector('.minutes');
                const secondsEl = countdown.querySelector('.seconds');
                
                if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
            }
        });
    }

    // Initialize countdowns
    if (document.querySelectorAll('.upcoming-ct').length > 0) {
        updateCountdowns();
        // Update countdowns every second
        setInterval(updateCountdowns, 1000);
    }
</script>
@endsection
