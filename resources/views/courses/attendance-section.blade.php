{{-- Attendance Section --}}
<script>
console.log('Auth guard check - Teacher:', {{ Auth::guard('teacher')->check() ? 'true' : 'false' }});
console.log('Auth guard check - Student:', {{ Auth::guard('student')->check() ? 'true' : 'false' }});
</script>

@if(Auth::guard('teacher')->check())
<div class="attendance-header">
    <h2>Attendance Management</h2>
    <p class="subtitle">Generate QR code for students to mark their attendance</p>
    
    <div class="header-actions">
        <button id="generateQRBtn" class="btn-primary">
            <span class="material-symbols-outlined">qr_code_2</span>
            Generate QR Code For Attendance
        </button>
    </div>
</div>

{{-- QR Code Display Area (Hidden by default) --}}
<div id="qrCodeSection" class="qr-code-section" style="display: none;">
    <div class="qr-container">
        <div class="qr-header">
            <h3>Scan QR Code to Mark Attendance</h3>
            <p class="qr-subtitle">{{ $course->course_code }} - {{ $course->course_title }}</p>
        </div>
        
        <div class="qr-content">
            {{-- QR Code Canvas --}}
            <div class="qr-code-wrapper">
                <div id="qrcode"></div>
            </div>
            
            {{-- Live Stats --}}
            <div class="attendance-stats">
                <div class="stat-card">
                    <span class="material-symbols-outlined">group</span>
                    <div class="stat-info">
                        <h4>Students Scanned</h4>
                        <p class="stat-count">
                            <span id="presentCount">0</span> / <span id="totalCount">{{ $course->students->count() }}</span>
                        </p>
                    </div>
                </div>
                
                <div class="stat-card timer-card">
                    <span class="material-symbols-outlined">timer</span>
                    <div class="stat-info">
                        <h4>Time Remaining</h4>
                        <p class="stat-time" id="timerDisplay">10:00</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="qr-actions">
            <button id="closeQRBtn" class="btn-secondary">
                <span class="material-symbols-outlined">close</span>
                Close Session
            </button>
        </div>
    </div>
</div>

{{-- Attendance History --}}
<div class="attendance-history">
    <h3>Recent Attendance Sessions</h3>
    <div class="history-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Attendance Rate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sessions = $course->attendanceSessions()->latest()->take(10)->get();
                @endphp
                @forelse($sessions as $session)
                <tr>
                    <td>{{ $session->started_at->format('M d, Y') }}</td>
                    <td class="present-count">{{ $session->present_count }}</td>
                    <td class="absent-count">{{ $session->total_students - $session->present_count }}</td>
                    <td>
                        @php
                            $rate = $session->total_students > 0 ? 
                                round(($session->present_count / $session->total_students) * 100) : 0;
                        @endphp
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $rate }}%"></div>
                            <span class="progress-text">{{ $rate }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($session->is_active && $session->isValid())
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-closed">Closed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-row">No attendance sessions yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Manual Attendance Sheet View --}}
<div class="manual-attendance-section">
    <h2>Attendance Sheet</h2>
    <p class="subtitle">View and manage student attendance marks</p>
    
    <div class="attendance-table-container" id="attendanceTableContainer">
        <div class="loading-spinner" id="attendanceLoader">Loading attendance data...</div>
        <div id="attendanceTableWrapper" style="display: none;">
            <div class="table-header-actions">
                <button id="calculateMarksBtn" class="btn-primary">
                    <span class="material-symbols-outlined">calculate</span>
                    Calculate Marks
                </button>
            </div>
            <div class="table-responsive">
                <table class="attendance-sheet-table" id="attendanceSheetTable">
                    <thead id="attendanceTableHead">
                        <!-- Will be populated dynamically -->
                    </thead>
                    <tbody id="attendanceTableBody">
                        <!-- Will be populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@elseif(Auth::guard('student')->check())
{{-- Student View --}}
<div class="attendance-header">
    <h2>My Attendance</h2>
    <p class="subtitle">View your attendance record and mark attendance when available</p>
</div>

{{-- Give Attendance Button --}}
<div class="attendance-action">
    <button type="button" id="giveAttendanceBtn" class="btn-primary btn-large">
        <span class="material-symbols-outlined">qr_code_scanner</span>
        Give Attendance
    </button>
    <p class="action-hint">Click to scan QR code when teacher starts attendance session</p>
</div>

{{-- QR Scanner Modal (Hidden by default) --}}
<div id="qrScannerModal" class="modal" style="display: none;">
    <div class="modal-content scanner-modal">
        <div class="modal-header">
            <h3>Scan QR Code</h3>
            <button class="modal-close" id="closeScannerBtn">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div id="qr-reader" style="width: 100%;"></div>
            <p class="scanner-hint">Position the QR code within the frame</p>
        </div>
    </div>
</div>

{{-- Attendance Calendar --}}
<div class="attendance-calendar">
    <h3>Attendance Calendar</h3>
    <div id="calendar"></div>
</div>

{{-- Attendance Stats Summary --}}
<div class="attendance-summary">
    <h3>Attendance Summary</h3>
    <div class="summary-cards">
        <div class="summary-card">
            <span class="material-symbols-outlined card-icon-present">check_circle</span>
            <div class="summary-info">
                <h4>Present</h4>
                <p class="summary-value" id="totalPresent">0</p>
            </div>
        </div>
        <div class="summary-card">
            <span class="material-symbols-outlined card-icon-absent">cancel</span>
            <div class="summary-info">
                <h4>Absent</h4>
                <p class="summary-value" id="totalAbsent">0</p>
            </div>
        </div>
        <div class="summary-card">
            <span class="material-symbols-outlined card-icon-rate">percent</span>
            <div class="summary-info">
                <h4>Attendance Rate</h4>
                <p class="summary-value" id="attendanceRate">0%</p>
            </div>
        </div>
        <div class="summary-card">
            <span class="material-symbols-outlined card-icon-marks">grade</span>
            <div class="summary-info">
                <h4>Attendance Marks</h4>
                <p class="summary-value" id="attendanceMarks">0</p>
            </div>
        </div>
    </div>
</div>

@endif

<style>
/* Attendance Section Styles */
.attendance-header {
    margin-bottom: 2rem;
}

.attendance-header h2 {
    color: #F1F5FB;
    margin-bottom: 0.5rem;
}

.attendance-header .subtitle {
    color: #C1CEE5;
    margin-bottom: 1.5rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* QR Code Section */
.qr-code-section {
    margin: 2rem 0;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.qr-container {
    background: linear-gradient(135deg, rgba(64, 26, 117, 0.2), rgba(94, 42, 158, 0.1));
    border: 2px solid rgba(193, 206, 229, 0.3);
    border-radius: 16px;
    padding: 2rem;
}

.qr-header {
    text-align: center;
    margin-bottom: 2rem;
}

.qr-header h3 {
    color: #F1F5FB;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.qr-subtitle {
    color: #C1CEE5;
    font-size: 1rem;
}

.qr-content {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 3rem;
    align-items: center;
}

.qr-code-wrapper {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

#qrcode {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Attendance Stats */
.attendance-stats {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border: 1px solid rgba(193, 206, 229, 0.2);
}

.stat-card .material-symbols-outlined {
    font-size: 3rem;
    color: #10b981;
}

.timer-card .material-symbols-outlined {
    color: #fbbf24;
}

.stat-info h4 {
    color: #C1CEE5;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.stat-count {
    color: #F1F5FB;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.stat-time {
    color: #fbbf24;
    font-size: 2.5rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    margin: 0;
}

/* QR Actions */
.qr-actions {
    margin-top: 2rem;
    text-align: center;
}

/* Attendance History */
.attendance-history {
    margin-top: 3rem;
}

.attendance-history h3 {
    color: #F1F5FB;
    margin-bottom: 1rem;
}

.history-table-wrapper {
    overflow-x: auto;
    background: rgba(38, 41, 54, 0.5);
    border-radius: 12px;
    padding: 1rem;
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
    color: #F1F5FB;
}

.attendance-table thead {
    background: linear-gradient(135deg, rgba(64, 26, 117, 0.3), rgba(94, 42, 158, 0.3));
}

.attendance-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid rgba(193, 206, 229, 0.2);
}

.attendance-table tbody tr {
    border-bottom: 1px solid rgba(193, 206, 229, 0.1);
    transition: background 0.2s;
}

.attendance-table tbody tr:hover {
    background: rgba(193, 206, 229, 0.05);
}

.attendance-table td {
    padding: 1rem;
}

.present-count {
    color: #10b981;
    font-weight: 600;
}

.absent-count {
    color: #ef4444;
    font-weight: 600;
}

.progress-bar {
    position: relative;
    width: 100%;
    height: 24px;
    background: rgba(193, 206, 229, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    transition: width 0.3s;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #F1F5FB;
    font-weight: 600;
    font-size: 0.85rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-active {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.status-closed {
    background: rgba(193, 206, 229, 0.2);
    color: #C1CEE5;
}

.empty-row {
    text-align: center;
    color: #C1CEE5;
    padding: 2rem !important;
}

/* Student View Styles */
.attendance-action {
    text-align: center;
    margin: 2rem 0;
    padding: 2rem;
    background: linear-gradient(135deg, rgba(64, 26, 117, 0.2), rgba(94, 42, 158, 0.1));
    border-radius: 12px;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.action-hint {
    color: #C1CEE5;
    margin-top: 1rem;
    font-size: 0.9rem;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #262936;
    border-radius: 16px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.scanner-modal {
    max-width: 500px;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(193, 206, 229, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: #F1F5FB;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: #C1CEE5;
    cursor: pointer;
    padding: 0.5rem;
    transition: color 0.2s;
}

.modal-close:hover {
    color: #F1F5FB;
}

.modal-body {
    padding: 1.5rem;
}

.scanner-hint {
    text-align: center;
    color: #C1CEE5;
    margin-top: 1rem;
}

/* Calendar Styles */
.attendance-calendar {
    margin: 2rem 0;
}

.attendance-calendar h3 {
    color: #F1F5FB;
    margin-bottom: 1rem;
}

#calendar {
    background: rgba(38, 41, 54, 0.5);
    border-radius: 12px;
    padding: 1rem;
}

/* Attendance Summary */
.attendance-summary {
    margin: 2rem 0;
}

.attendance-summary h3 {
    color: #F1F5FB;
    margin-bottom: 1rem;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.summary-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border: 1px solid rgba(193, 206, 229, 0.1);
}

.card-icon-present {
    font-size: 3rem;
    color: #10b981;
}

.card-icon-absent {
    font-size: 3rem;
    color: #ef4444;
}

.card-icon-rate {
    font-size: 3rem;
    color: #fbbf24;
}

.summary-info h4 {
    color: #C1CEE5;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.summary-value {
    color: #F1F5FB;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

/* Manual Attendance Sheet Styles */
.manual-attendance-section {
    margin-top: 3rem;
    background: rgba(38, 41, 54, 0.5);
    border-radius: 15px;
    padding: 2rem;
}

.manual-attendance-section h2 {
    color: #F1F5FB;
    margin: 0 0 0.5rem 0;
}

.manual-attendance-section .subtitle {
    color: #C1CEE5;
    margin: 0 0 1.5rem 0;
}

.attendance-table-container {
    background: rgba(20, 23, 32, 0.6);
    border-radius: 12px;
    padding: 1.5rem;
}

.loading-spinner {
    text-align: center;
    padding: 3rem;
    color: #C1CEE5;
    font-size: 1.1rem;
}

.table-header-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(193, 206, 229, 0.1);
}

.table-header-actions .btn-primary {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
}

.table-header-actions .btn-primary .material-symbols-outlined {
    font-size: 1.2rem;
}

.total-marks-display {
    color: #C1CEE5;
    font-size: 1.1rem;
    font-weight: 600;
}

.total-marks-display span {
    color: #7c3aed;
    font-size: 1.3rem;
}

.table-responsive {
    overflow-x: auto;
}

.attendance-sheet-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(38, 41, 54, 0.3);
    border-radius: 8px;
}

.attendance-sheet-table thead {
    background: linear-gradient(135deg, #7c3aed 0%, #401a75 100%);
}

.attendance-sheet-table th {
    padding: 1rem;
    text-align: left;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.attendance-sheet-table th.date-column {
    text-align: center;
    min-width: 80px;
}

.attendance-sheet-table tbody tr {
    border-bottom: 1px solid rgba(193, 206, 229, 0.1);
    transition: background 0.2s ease;
}

.attendance-sheet-table tbody tr:hover {
    background: rgba(124, 58, 237, 0.1);
}

.attendance-sheet-table td {
    padding: 0.9rem 1rem;
    color: #F1F5FB;
}

.attendance-sheet-table td.date-cell {
    text-align: center;
    font-weight: 600;
    font-size: 0.95rem;
}

.attendance-sheet-table td.present {
    color: #10b981;
}

.attendance-sheet-table td.absent {
    color: #ef4444;
}

.attendance-sheet-table td.percentage-cell {
    font-weight: 600;
    color: #7c3aed;
}

.attendance-sheet-table td.marks-cell {
    font-weight: 700;
    color: #f59e0b;
    font-size: 1.05rem;
}

.card-icon-marks {
    color: #f59e0b;
    font-size: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .qr-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .attendance-stats {
        width: 100%;
    }
    
    .table-header-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .attendance-sheet-table {
        font-size: 0.85rem;
    }
    
    .attendance-sheet-table th,
    .attendance-sheet-table td {
        padding: 0.6rem 0.5rem;
    }
}
</style>

{{-- JavaScript for Attendance --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

@if(Auth::guard('teacher')->check())
<script>
// Teacher: QR Code Generation
let currentSession = null;
let statusInterval = null;
let timerInterval = null;

// Check for active session on page load
function checkActiveSession() {
    console.log('Checking for active session on page load...');
    fetch(`/courses/{{ $course->id }}/attendance/active`)
        .then(response => response.json())
        .then(data => {
            console.log('Active session check response:', data);
            if (data.has_session && data.session) {
                console.log('Active session found, restoring QR...');
                // Restore the active session
                currentSession = {
                    session_id: data.session.id,
                    qr_token: data.session.qr_code,
                    expires_at: data.session.expires_at
                };
                
                // Show QR section
                document.getElementById('qrCodeSection').style.display = 'block';
                document.getElementById('generateQRBtn').style.display = 'none';
                
                // Generate QR code
                document.getElementById('qrcode').innerHTML = '';
                new QRCode(document.getElementById('qrcode'), {
                    text: currentSession.qr_token,
                    width: 256,
                    height: 256,
                    colorDark: "#401a75",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
                
                // Calculate remaining time
                const expiresAt = new Date(currentSession.expires_at);
                const now = new Date();
                const remainingSeconds = Math.max(0, Math.floor((expiresAt - now) / 1000));
                console.log('Remaining seconds:', remainingSeconds);
                
                // Start live updates and timer
                startStatusUpdates();
                startTimer(remainingSeconds);
            } else {
                console.log('No active session found');
            }
        })
        .catch(error => console.error('Error checking active session:', error));
}

// Call on page load
console.log('Page loaded, will check for active session...');
checkActiveSession();

document.getElementById('generateQRBtn').addEventListener('click', function() {
    // Generate QR Code
    fetch(`/courses/{{ $course->id }}/attendance/generate-qr`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentSession = data;
            
            // Show QR section
            document.getElementById('qrCodeSection').style.display = 'block';
            
            // Clear previous QR code
            document.getElementById('qrcode').innerHTML = '';
            
            // Generate QR code
            new QRCode(document.getElementById('qrcode'), {
                text: data.qr_token,
                width: 256,
                height: 256,
                colorDark: "#401a75",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            // Start live updates
            startStatusUpdates();
            startTimer(600); // 10 minutes = 600 seconds
            
            // Hide generate button
            this.style.display = 'none';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to generate QR code');
    });
});

function startStatusUpdates() {
    statusInterval = setInterval(() => {
        if (!currentSession) return;
        
        fetch(`/attendance/session/${currentSession.session_id}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('presentCount').textContent = data.present_count;
                    
                    if (!data.is_valid) {
                        // Session expired
                        stopSession();
                    }
                }
            });
    }, 2000); // Update every 2 seconds
}

function startTimer(seconds) {
    let remaining = seconds;
    
    function updateTimer() {
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;
        document.getElementById('timerDisplay').textContent = 
            `${minutes}:${secs.toString().padStart(2, '0')}`;
        
        if (remaining <= 0) {
            stopSession();
        } else {
            remaining--;
        }
    }
    
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
}

function stopSession() {
    clearInterval(statusInterval);
    clearInterval(timerInterval);
    
    // Hide QR section
    document.getElementById('qrCodeSection').style.display = 'none';
    
    // Show generate button again
    document.getElementById('generateQRBtn').style.display = 'inline-flex';
    
    currentSession = null;
    
    // Reload page to show updated history
    setTimeout(() => location.reload(), 1000);
}

document.getElementById('closeQRBtn').addEventListener('click', function() {
    if (!currentSession) return;
    
    if (confirm('Are you sure you want to close this attendance session?')) {
        fetch(`/attendance/session/${currentSession.session_id}/close`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                stopSession();
            }
        });
    }
});
</script>

<!-- Manual Attendance Sheet JavaScript -->
<script>
// Load attendance sheet data
function loadAttendanceSheet() {
    const loader = document.getElementById('attendanceLoader');
    const wrapper = document.getElementById('attendanceTableWrapper');
    
    fetch(`/courses/{{ $course->id }}/attendance/sheet`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAttendanceSheet(data);
                loader.style.display = 'none';
                wrapper.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading attendance sheet:', error);
            loader.innerHTML = 'Failed to load attendance data';
        });
}

function displayAttendanceSheet(data) {
    const thead = document.getElementById('attendanceTableHead');
    const tbody = document.getElementById('attendanceTableBody');
    
    // Build table header
    let headerHTML = '<tr><th>Roll</th><th>Name</th>';
    data.dates.forEach(date => {
        const dateObj = new Date(date);
        const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        headerHTML += `<th class="date-column">${formattedDate}</th>`;
    });
    headerHTML += '<th>Present</th><th>%</th><th>Marks</th></tr>';
    thead.innerHTML = headerHTML;
    
    // Build table body
    let bodyHTML = '';
    data.students.forEach(student => {
        bodyHTML += `<tr>
            <td>${student.roll}</td>
            <td>${student.name}</td>`;
        
        data.dates.forEach(date => {
            const status = student.attendance[date] || 'A';
            const className = status === 'P' ? 'date-cell present' : 'date-cell absent';
            bodyHTML += `<td class="${className}">${status}</td>`;
        });
        
        bodyHTML += `
            <td>${student.present_count}</td>
            <td class="percentage-cell">${student.percentage}%</td>
            <td class="marks-cell">${student.marks || '-'}</td>
        </tr>`;
    });
    tbody.innerHTML = bodyHTML;
}

// Calculate marks button
document.getElementById('calculateMarksBtn').addEventListener('click', function() {
    if (!confirm('This will calculate and update attendance marks for all students based on their attendance percentage. Continue?')) {
        return;
    }
    
    this.disabled = true;
    this.innerHTML = '<span class="material-symbols-outlined">hourglass_empty</span> Calculating...';
    
    fetch(`/courses/{{ $course->id }}/attendance/calculate-marks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadAttendanceSheet(); // Reload the table
        } else {
            alert(data.message);
        }
        this.disabled = false;
        this.innerHTML = '<span class="material-symbols-outlined">calculate</span> Calculate Marks';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to calculate marks');
        this.disabled = false;
        this.innerHTML = '<span class="material-symbols-outlined">calculate</span> Calculate Marks';
    });
});

// Load on page load
loadAttendanceSheet();
</script>
@endif

@if(Auth::guard('student')->check())
<script>
console.log('=== STUDENT SCRIPT START ===');

try {
    console.log('Step 1: Script executing');
    
    let html5QrCode = null;
    console.log('Step 2: Variable declared');
    
    function initStudentAttendance() {
        console.log('Step 3: Inside initStudentAttendance function');
        
        const giveAttendanceBtn = document.getElementById('giveAttendanceBtn');
        console.log('Step 4: Button search result:', giveAttendanceBtn);
    
    if (giveAttendanceBtn) {
        console.log('Step 5: Button found, adding click listener');
        giveAttendanceBtn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            console.log('BUTTON CLICKED!!!');
            
            // Check if there's an active session
            fetch(`/courses/{{ $course->id }}/attendance/active`)
                .then(response => response.json())
                .then(data => {
                    console.log('Active session response:', data);
                    
                    if (data.has_session) {
                        if (data.already_marked) {
                            alert('You have already marked your attendance for this session!');
                        } else {
                            // Show scanner modal
                            document.getElementById('qrScannerModal').style.display = 'flex';
                            startScanner();
                        }
                    } else {
                        alert('No active attendance session. Please wait for your teacher to start one.');
                    }
                })
                .catch(error => {
                    console.error('Error checking active session:', error);
                    alert('Error checking for active session. Please try again.');
                });
        });
    } else {
        console.error('Give Attendance button not found!');
    }

    function startScanner() {
        console.log('Starting scanner...');
        
        html5QrCode = new Html5Qrcode("qr-reader");
        
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText) => {
                // QR code scanned successfully
                console.log('QR scanned:', decodedText);
                markAttendance(decodedText);
            },
            (error) => {
                // Scanning errors (ignore - happens continuously while searching for QR)
            }
        ).catch(err => {
            console.error('Unable to start camera:', err);
            alert('Unable to access camera. Please check permissions and make sure you are using HTTPS.');
            stopScanner();
        });
    }

    function stopScanner() {
        console.log('Stopping scanner...');
        
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(err => {
                console.error('Error stopping scanner:', err);
            });
        }
        document.getElementById('qrScannerModal').style.display = 'none';
    }

    const closeScannerBtn = document.getElementById('closeScannerBtn');
    if (closeScannerBtn) {
        closeScannerBtn.addEventListener('click', stopScanner);
    }

    function markAttendance(qrToken) {
        fetch('/attendance/mark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                qr_token: qrToken
            })
        })
        .then(response => response.json())
        .then(data => {
            stopScanner();
            
            if (data.success) {
                alert(data.message);
                loadAttendanceData(); // Reload calendar
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            stopScanner();
            console.error('Error:', error);
            alert('Failed to mark attendance');
        });
    }

    // Load attendance data for calendar
    function loadAttendanceData() {
        fetch(`/courses/{{ $course->id }}/attendance/data`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCalendar(data.attendances);
                    updateSummary(data);
                }
            });
    }

    function updateCalendar(attendances) {
    // Simple calendar rendering
    const calendar = document.getElementById('calendar');
    let html = '<div class="calendar-grid">';
    
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    // Get first day of month
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    
    // Day names
    html += '<div class="calendar-header">';
    ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
        html += `<div class="calendar-day-name">${day}</div>`;
    });
    html += '</div>';
    
    // Empty cells for days before month starts
    html += '<div class="calendar-days">';
    for (let i = 0; i < firstDay.getDay(); i++) {
        html += '<div class="calendar-cell empty"></div>';
    }
    
    // Days of month
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const attendance = attendances.find(a => a.date === dateStr);
        
        let className = 'calendar-cell';
        if (attendance) {
            className += attendance.status === 'present' ? ' present' : ' absent';
        }
        
        html += `<div class="${className}">
            <div class="cell-date">${day}</div>
            ${attendance ? `<div class="cell-status">${attendance.status === 'present' ? '✓' : '✗'}</div>` : ''}
        </div>`;
    }
    
    html += '</div></div>';
    
    html += `
    <style>
    .calendar-grid {
        background: rgba(38, 41, 54, 0.5);
        border-radius: 12px;
        padding: 1rem;
    }
    .calendar-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .calendar-day-name {
        text-align: center;
        color: #C1CEE5;
        font-weight: 600;
        padding: 0.5rem;
    }
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
    }
    .calendar-cell {
        aspect-ratio: 1;
        background: rgba(193, 206, 229, 0.05);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        position: relative;
    }
    .calendar-cell.empty {
        background: transparent;
    }
    .calendar-cell.present {
        background: rgba(16, 185, 129, 0.2);
        border: 2px solid #10b981;
    }
    .calendar-cell.absent {
        background: rgba(239, 68, 68, 0.2);
        border: 2px solid #ef4444;
    }
    .cell-date {
        color: #F1F5FB;
        font-weight: 600;
    }
    .cell-status {
        font-size: 1.2rem;
        margin-top: 0.25rem;
    }
    .present .cell-status {
        color: #10b981;
    }
    .absent .cell-status {
        color: #ef4444;
    }
    </style>
    `;
    
    calendar.innerHTML = html;
}

function updateSummary(data) {
    // Use day-based counts from API response
    const presentDays = data.present_days || 0;
    const totalDays = data.total_days || 0;
    const absentDays = totalDays - presentDays;
    const rate = data.percentage || 0;
    
    // Get marks from the first attendance record that has marks
    const attendances = data.attendances || [];
    const marksRecord = attendances.find(a => a.marks !== null && a.marks !== undefined);
    const marks = marksRecord ? marksRecord.marks : 0;
    
    document.getElementById('totalPresent').textContent = presentDays;
    document.getElementById('totalAbsent').textContent = absentDays;
    document.getElementById('attendanceRate').textContent = rate + '%';
    document.getElementById('attendanceMarks').textContent = marks;
    }

    // Load data on page load
    console.log('Step 6: About to load attendance data');
    loadAttendanceData();
    console.log('Step 7: initStudentAttendance complete');
    } // End initStudentAttendance
    
    console.log('Step 8: About to call initStudentAttendance');
    initStudentAttendance();
    console.log('Step 9: Script complete');
    
} catch(error) {
    console.error('ERROR IN STUDENT SCRIPT:', error);
}
</script>
@endif
