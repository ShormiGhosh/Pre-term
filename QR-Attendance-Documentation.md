# 📋 QR Code-Based Attendance System - Complete Documentation

## 🎯 Overview

A real-time, QR code-based attendance system that enables:
- **Teachers**: Generate time-limited QR codes, monitor live scanning progress, and view attendance history
- **Students**: Scan QR codes to mark attendance, view attendance calendar, and track attendance rate
- **Features**: 10-minute auto-expiry, live student counter, visual calendar, automated absent marking

---

## ✨ Key Features

### For Teachers:
✅ **One-Click QR Generation** - Generate unique QR code for each attendance session  
✅ **Live Student Counter** - See how many students have scanned in real-time  
✅ **10-Minute Timer** - Countdown timer with auto-close after expiration  
✅ **Manual Close** - Option to close session before timer expires  
✅ **Attendance History** - View past sessions with attendance rates  
✅ **Auto-Absent Marking** - Students who don't scan are automatically marked absent  

### For Students:
✅ **QR Scanner** - Built-in camera scanner to mark attendance  
✅ **Visual Calendar** - Green (present) and Red (absent) color-coded calendar  
✅ **Attendance Summary** - Total present/absent days and attendance rate  
✅ **Active Session Check** - Know when attendance is available  
✅ **Already Marked Prevention** - Can't mark attendance twice for same session  

---

## 📁 File Structure

```
pre-term/
│
├── app/
│   ├── Http/Controllers/
│   │   └── AttendanceController.php         (handles all attendance logic)
│   │
│   └── Models/
│       ├── AttendanceSession.php             (QR session model)
│       ├── Attendance.php                    (individual attendance record)
│       ├── Course.php                        (updated with relationships)
│       └── Student.php                       (updated with relationships)
│
├── database/migrations/
│   ├── 2025_10_25_090625_create_attendance_sessions_table.php
│   └── 2025_10_25_090644_create_attendances_table.php
│
├── resources/views/courses/
│   ├── attendance-section.blade.php          (attendance UI component)
│   └── show.blade.php                        (updated to include attendance)
│
└── routes/
    └── web.php                                (attendance routes)
```

---

## 🗄️ Database Schema

### attendance_sessions Table

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | BIGINT | No | Primary key |
| course_id | BIGINT | No | Foreign key to courses |
| teacher_id | BIGINT | No | Foreign key to teachers |
| qr_code | VARCHAR | No | Unique token for QR code (32 chars) |
| started_at | DATETIME | No | When session started |
| expires_at | DATETIME | No | When session expires (started_at + 10 min) |
| is_active | BOOLEAN | No | Whether session is active (default: true) |
| created_at | TIMESTAMP | Yes | Record creation time |
| updated_at | TIMESTAMP | Yes | Record update time |

**Constraints:**
- Unique on `qr_code`
- Foreign keys with CASCADE DELETE
- Index on `(course_id, is_active, expires_at)` for performance

**Example Record:**
```php
{
    "id": 1,
    "course_id": 5,
    "teacher_id": 2,
    "qr_code": "a7f8e9c2b1d4f6g8h2j3k5m7n9p1q4r6",
    "started_at": "2025-10-25 14:30:00",
    "expires_at": "2025-10-25 14:40:00",
    "is_active": true
}
```

---

### attendances Table

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | BIGINT | No | Primary key |
| attendance_session_id | BIGINT | No | Foreign key to attendance_sessions |
| student_id | BIGINT | No | Foreign key to students |
| course_id | BIGINT | No | Foreign key to courses |
| status | ENUM | No | 'present' or 'absent' (default: 'absent') |
| marked_at | DATETIME | Yes | When student scanned QR (NULL if absent) |
| created_at | TIMESTAMP | Yes | Record creation time |
| updated_at | TIMESTAMP | Yes | Record update time |

**Constraints:**
- Unique on `(attendance_session_id, student_id)` - Prevents duplicate attendance
- Foreign keys with CASCADE DELETE

**Example Records:**
```php
[
    {
        "id": 1,
        "attendance_session_id": 1,
        "student_id": 10,
        "course_id": 5,
        "status": "present",
        "marked_at": "2025-10-25 14:32:15"
    },
    {
        "id": 2,
        "attendance_session_id": 1,
        "student_id": 11,
        "course_id": 5,
        "status": "absent",
        "marked_at": null
    }
]
```

---

## 🔗 Models & Relationships

### AttendanceSession Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'course_id', 'teacher_id', 'qr_code', 
        'started_at', 'expires_at', 'is_active'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function course() { return $this->belongsTo(Course::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }

    // Helper methods
    public function isValid() {
        return $this->is_active && now()->lessThan($this->expires_at);
    }

    public function getPresentCountAttribute() {
        return $this->attendances()->where('status', 'present')->count();
    }

    public function getTotalStudentsAttribute() {
        return $this->course->students()->count();
    }
}
```

**Key Features:**
- `isValid()` - Checks if session is still active and not expired
- `present_count` - Accessor to get count of students who marked attendance
- `total_students` - Accessor to get total enrolled students

---

### Attendance Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_session_id', 'student_id', 
        'course_id', 'status', 'marked_at'
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    // Relationships
    public function attendanceSession() { 
        return $this->belongsTo(AttendanceSession::class); 
    }
    
    public function student() { 
        return $this->belongsTo(Student::class); 
    }
    
    public function course() { 
        return $this->belongsTo(Course::class); 
    }
}
```

---

## 🛣️ Routes Configuration

```php
// Teacher Routes (protected by teacher.auth middleware)
Route::middleware(['teacher.auth'])->group(function () {
    // Generate new QR code session
    Route::post('/courses/{courseId}/attendance/generate-qr', 
        [AttendanceController::class, 'generateQR'])
        ->name('attendance.generate-qr');
    
    // Get live session status (for real-time updates)
    Route::get('/attendance/session/{sessionId}/status', 
        [AttendanceController::class, 'getSessionStatus'])
        ->name('attendance.session.status');
    
    // Manually close session
    Route::post('/attendance/session/{sessionId}/close', 
        [AttendanceController::class, 'closeSession'])
        ->name('attendance.session.close');
});

// Student Routes (protected by student.auth middleware)
Route::middleware(['student.auth'])->group(function () {
    // Mark attendance by scanning QR
    Route::post('/attendance/mark', 
        [AttendanceController::class, 'markAttendance'])
        ->name('attendance.mark');
    
    // Check if there's an active session
    Route::get('/courses/{courseId}/attendance/active', 
        [AttendanceController::class, 'getActiveSession'])
        ->name('attendance.active');
    
    // Get student's attendance data for calendar
    Route::get('/courses/{courseId}/attendance/data', 
        [AttendanceController::class, 'getStudentAttendance'])
        ->name('attendance.student.data');
});
```

---

## 🎮 Controller Methods

### 1. generateQR() - Teacher Creates Attendance Session

**Purpose:** Generate a new QR code for attendance  
**Method:** POST  
**Route:** `/courses/{courseId}/attendance/generate-qr`

**Flow:**
```
1. Verify teacher owns the course
2. Deactivate any existing active sessions for this course
3. Generate unique 32-character token using Str::random(32)
4. Create AttendanceSession record:
   - started_at: now()
   - expires_at: now() + 10 minutes
   - is_active: true
5. Create Attendance records for all enrolled students:
   - status: 'absent' (default)
   - marked_at: null
6. Return session data (id, qr_token, expires_at)
```

**Response:**
```json
{
    "success": true,
    "session_id": 15,
    "qr_token": "a7f8e9c2b1d4f6g8h2j3k5m7n9p1q4r6",
    "expires_at": "2025-10-25T14:40:00.000000Z"
}
```

---

### 2. getSessionStatus() - Live Updates for Teacher

**Purpose:** Get real-time student count and time remaining  
**Method:** GET  
**Route:** `/attendance/session/{sessionId}/status`

**Response:**
```json
{
    "success": true,
    "present_count": 15,
    "total_students": 30,
    "is_valid": true,
    "time_remaining": 480
}
```

**Usage:** Called every 2 seconds via JavaScript to update UI

---

### 3. markAttendance() - Student Scans QR

**Purpose:** Mark student as present when they scan QR code  
**Method:** POST  
**Route:** `/attendance/mark`

**Request:**
```json
{
    "qr_token": "a7f8e9c2b1d4f6g8h2j3k5m7n9p1q4r6"
}
```

**Flow:**
```
1. Find active session with given qr_token
2. Validate session exists and is not expired
3. Check student is enrolled in the course
4. Update attendance record:
   - status: 'present'
   - marked_at: now()
5. Return success message
```

**Response:**
```json
{
    "success": true,
    "message": "Attendance marked successfully!",
    "marked_at": "02:32 PM"
}
```

**Error Responses:**
```json
// Invalid/expired QR
{
    "success": false,
    "message": "Invalid or expired QR code."
}

// Already marked
{
    "success": false,
    "message": "You have already marked your attendance for this session!"
}

// Not enrolled
{
    "success": false,
    "message": "You are not enrolled in this course."
}
```

---

### 4. getActiveSession() - Check for Active Attendance

**Purpose:** Check if teacher has started attendance session  
**Method:** GET  
**Route:** `/courses/{courseId}/attendance/active`

**Response (Active):**
```json
{
    "success": true,
    "has_session": true,
    "already_marked": false,
    "time_remaining": 540
}
```

**Response (No Active Session):**
```json
{
    "success": false,
    "message": "No active attendance session.",
    "has_session": false
}
```

---

### 5. getStudentAttendance() - Get Calendar Data

**Purpose:** Fetch all attendance records for student calendar  
**Method:** GET  
**Route:** `/courses/{courseId}/attendance/data`

**Response:**
```json
{
    "success": true,
    "attendances": [
        {
            "date": "2025-10-25",
            "time": "02:30 PM",
            "status": "present",
            "marked_at": "02:32 PM"
        },
        {
            "date": "2025-10-24",
            "time": "09:00 AM",
            "status": "absent",
            "marked_at": null
        }
    ]
}
```

---

### 6. closeSession() - Manually End Session

**Purpose:** Teacher manually closes attendance before timer expires  
**Method:** POST  
**Route:** `/attendance/session/{sessionId}/close`

**Response:**
```json
{
    "success": true,
    "message": "Attendance session closed."
}
```

---

## 🎨 User Interface

### Teacher View

#### QR Code Section
```
┌────────────────────────────────────────┐
│   Scan QR Code to Mark Attendance      │
│   CSE 3100 - Software Engineering      │
├────────────────────────────────────────┤
│                                        │
│   ┌──────────┐    ┌──────────────┐   │
│   │          │    │ Students      │   │
│   │   QR     │    │ Scanned       │   │
│   │   CODE   │    │               │   │
│   │          │    │  15 / 30      │   │
│   └──────────┘    └──────────────┘   │
│                                        │
│                   ┌──────────────┐   │
│                   │ Time          │   │
│                   │ Remaining     │   │
│                   │               │   │
│                   │   08:45       │   │
│                   └──────────────┘   │
│                                        │
│          [ Close Session ]             │
└────────────────────────────────────────┘
```

#### Attendance History Table
```
┌──────────────────────────────────────────────────────────────┐
│ Date        Time      Present  Absent  Attendance Rate  Status│
├──────────────────────────────────────────────────────────────┤
│ Oct 25, 25  02:30 PM    15       15      [████████░░] 50%  Closed│
│ Oct 24, 25  09:00 AM    28        2      [█████████░] 93%  Closed│
│ Oct 23, 25  02:30 PM    25        5      [████████░░] 83%  Closed│
└──────────────────────────────────────────────────────────────┘
```

---

### Student View

#### Give Attendance Button
```
┌────────────────────────────────────────┐
│                                        │
│        [ QR Scanner Icon ]             │
│                                        │
│         Give Attendance                │
│                                        │
│  Click to scan QR code when teacher   │
│      starts attendance session         │
│                                        │
└────────────────────────────────────────┘
```

#### Attendance Calendar (October 2025)
```
┌────────────────────────────────────────┐
│  Sun  Mon  Tue  Wed  Thu  Fri  Sat    │
├────────────────────────────────────────┤
│             1    2    3    4    5     │
│  6    7    8    9   10   11   12     │
│ 13   14   15   16   17   18   19     │
│ 20   21   22   23   24   25   26     │
│                  🟢   🟢   🔴         │
│ 27   28   29   30   31               │
└────────────────────────────────────────┘

🟢 = Present (Green background)
🔴 = Absent (Red background)
```

#### Attendance Summary Cards
```
┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ ✓ Present   │ │ ✗ Absent    │ │ % Attend.   │
│             │ │             │ │   Rate      │
│     23      │ │      2      │ │             │
│             │ │             │ │    92%      │
└─────────────┘ └─────────────┘ └─────────────┘
```

---

## 🔄 Data Flow

### Teacher Workflow

```
1. Teacher clicks "Generate QR Code For Attendance"
   ↓
2. AJAX POST to /courses/{id}/attendance/generate-qr
   ↓
3. Controller:
   - Deactivates old sessions
   - Generates unique token (Str::random(32))
   - Creates AttendanceSession record
   - Creates Attendance records for all students (status: 'absent')
   ↓
4. Returns: { session_id, qr_token, expires_at }
   ↓
5. JavaScript:
   - Shows QR code section
   - Generates QR code image using QRCode.js
   - Starts live updates (every 2 seconds)
   - Starts countdown timer (10 minutes)
   ↓
6. Every 2 seconds:
   - AJAX GET to /attendance/session/{id}/status
   - Updates present count in UI
   - Checks if session expired
   ↓
7. Timer reaches 00:00 OR teacher clicks "Close Session":
   - AJAX POST to /attendance/session/{id}/close
   - Sets is_active = false
   - Hides QR section
   - Reloads page to show updated history
```

---

### Student Workflow

```
1. Student clicks "Give Attendance"
   ↓
2. AJAX GET to /courses/{id}/attendance/active
   ↓
3. Controller checks for active session:
   - If no session: "No active attendance session"
   - If already marked: "You have already marked..."
   - If session exists: Returns { has_session: true, time_remaining }
   ↓
4. JavaScript opens QR scanner modal
   - Activates camera using Html5Qrcode library
   - Shows live camera feed with QR detection box
   ↓
5. Student positions phone to scan teacher's QR code
   ↓
6. QR code detected:
   - Extracts qr_token from QR code
   - AJAX POST to /attendance/mark
   - Sends: { qr_token: "abc..." }
   ↓
7. Controller validates and marks attendance:
   - Finds session by qr_token
   - Checks if session is valid (not expired)
   - Checks if student is enrolled
   - Updates attendance record:
     * status: 'present'
     * marked_at: now()
   ↓
8. Returns success message
   ↓
9. JavaScript:
   - Closes scanner modal
   - Shows success alert
   - Reloads attendance calendar (updated with green mark)
   - Updates summary cards
```

---

## 💻 Technical Implementation

### QR Code Generation (Teacher Side)

**Library:** QRCode.js  
**CDN:** `https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js`

```javascript
new QRCode(document.getElementById('qrcode'), {
    text: data.qr_token,  // The unique token
    width: 256,
    height: 256,
    colorDark: "#401a75",  // Purple (brand color)
    colorLight: "#ffffff",  // White background
    correctLevel: QRCode.CorrectLevel.H  // High error correction
});
```

**What gets encoded:** Just the 32-character token (e.g., `a7f8e9c2b1d4f6g8h2j3k5m7n9p1q4r6`)

---

### QR Code Scanning (Student Side)

**Library:** Html5-QRCode  
**CDN:** `https://unpkg.com/html5-qrcode`

```javascript
const html5QrCode = new Html5Qrcode("qr-reader");

html5QrCode.start(
    { facingMode: "environment" },  // Use back camera
    {
        fps: 10,  // 10 frames per second
        qrbox: { width: 250, height: 250 }  // Detection box size
    },
    (decodedText) => {
        // Successfully scanned!
        markAttendance(decodedText);  // Send to server
    },
    (error) => {
        // Scanning errors (can be ignored)
    }
);
```

**Features:**
- Automatically accesses device camera
- Works on mobile browsers
- Real-time QR code detection
- No app installation required

---

### Live Updates (Polling)

**Teacher Side - Status Updates:**

```javascript
setInterval(() => {
    fetch(`/attendance/session/${sessionId}/status`)
        .then(response => response.json())
        .then(data => {
            // Update present count
            document.getElementById('presentCount').textContent = data.present_count;
            
            // Check if expired
            if (!data.is_valid) {
                stopSession();
            }
        });
}, 2000);  // Every 2 seconds
```

**Why 2 seconds?**
- Fast enough to feel "real-time"
- Not so fast that it overloads the server
- Balance between UX and performance

---

### Countdown Timer

```javascript
function startTimer(seconds) {
    let remaining = seconds;
    
    const interval = setInterval(() => {
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;
        
        // Display: 09:45, 08:30, etc.
        document.getElementById('timerDisplay').textContent = 
            `${minutes}:${secs.toString().padStart(2, '0')}`;
        
        if (remaining <= 0) {
            clearInterval(interval);
            stopSession();  // Auto-close when timer expires
        } else {
            remaining--;
        }
    }, 1000);  // Every 1 second
}
```

---

### Calendar Rendering

**Simple Grid-Based Calendar:**

```javascript
function updateCalendar(attendances) {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    // Render day names: Sun, Mon, Tue, ...
    // Render empty cells for days before month starts
    // Render each day of the month:
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const dateStr = `${year}-${month}-${day}`;
        const attendance = attendances.find(a => a.date === dateStr);
        
        // Apply CSS class based on status:
        // .present - Green background
        // .absent - Red background
    }
}
```

---

## 🔒 Security Features

### 1. QR Token Security
- **32-character random string** - Virtually impossible to guess
- **Unique constraint** - No duplicate tokens in database
- **Time-limited** - Expires after 10 minutes
- **Single-use per session** - Each session gets new token

### 2. Authorization Checks
```php
// Teacher must own the course
if ($course->teacher_id !== $teacher->id) {
    return response()->json(['success' => false], 403);
}

// Student must be enrolled
if (!$course->students()->where('student_id', $student->id)->exists()) {
    return response()->json(['success' => false], 403);
}
```

### 3. Validation
- **Session expiry** - Server checks `expires_at` before allowing scan
- **Duplicate prevention** - Unique constraint on `(session_id, student_id)`
- **Already marked check** - Can't scan twice for same session

### 4. CSRF Protection
- All POST requests require CSRF token
- Automatically included via `@csrf` directive
- Sent in AJAX headers: `'X-CSRF-TOKEN': token`

---

## 📊 Database Optimizations

### Indexes
```sql
-- For fast session lookups
CREATE INDEX idx_session_lookup 
ON attendance_sessions(course_id, is_active, expires_at);

-- For fast attendance queries
CREATE INDEX idx_attendance_student 
ON attendances(student_id, course_id);
```

### Preventing N+1 Queries
```php
// Bad (N+1 queries)
$sessions = AttendanceSession::all();
foreach ($sessions as $session) {
    echo $session->present_count;  // Queries for each session
}

// Good (2 queries total)
$sessions = AttendanceSession::withCount([
    'attendances as present_count' => function($query) {
        $query->where('status', 'present');
    }
])->get();
```

---

## 🧪 Testing Guide

### Manual Testing Steps

#### Teacher Flow:
1. Login as teacher
2. Navigate to a course
3. Click "Attendance Sheet" tab
4. Click "Generate QR Code For Attendance"
5. **Verify:**
   - QR code appears
   - Timer starts at 10:00
   - Present count shows 0 / X

6. Keep page open, check live updates
7. **Verify:**
   - Counter updates when students scan
   - Timer counts down every second

8. Wait for timer to reach 00:00 OR click "Close Session"
9. **Verify:**
   - QR section closes
   - Session appears in history table
   - Attendance rate calculated correctly

#### Student Flow:
1. Login as student
2. Navigate to enrolled course
3. Click "Attendance Sheet" tab
4. **Verify:** "Give Attendance" button visible

5. Click "Give Attendance"
6. **Verify:**
   - If no active session: "No active attendance session"
   - If session active: Camera opens

7. Grant camera permissions
8. Point camera at teacher's QR code
9. **Verify:**
   - QR detected automatically
   - Success message appears
   - Scanner closes

10. Check calendar
11. **Verify:**
    - Today's date shows green background
    - Summary cards updated
    - Attendance rate calculated

#### Edge Cases:
- Try scanning QR twice → Should show "already marked"
- Try scanning after 10 minutes → Should show "expired"
- Try scanning from unenrolled course → Should show "not enrolled"
- Try accessing teacher route as student → Should get 403 error

---

## 🎯 User Experience Details

### Teacher Experience

**Before Starting:**
- Clean interface showing only "Generate QR Code" button
- Recent attendance history visible

**During Session:**
- Large, clear QR code (256x256px)
- Live counter updating every 2 seconds
- Prominent countdown timer
- Visual feedback when students scan

**After Session:**
- Automatic cleanup
- Updated history table
- Attendance rate visualization

**Time Estimates:**
- Generate QR: < 1 second
- Students scan: 2-3 seconds each
- Session auto-close: Exactly 10 minutes

---

### Student Experience

**Checking for Attendance:**
- Single button: "Give Attendance"
- Clear hint text below button
- Immediate feedback if no session

**Scanning Process:**
1. Click button (< 1 second)
2. Camera opens (1-2 seconds)
3. Position QR code (1-2 seconds)
4. Auto-detect and submit (< 1 second)
5. Success message (instant)

**Total time:** ~5-7 seconds

**Calendar:**
- Month view at a glance
- Color-coded for quick recognition:
  - Green = Present = Good
  - Red = Absent = Warning
- Shows attendance pattern over time

---

## 🐛 Common Issues & Solutions

### Issue 1: Camera Not Working

**Symptoms:** "Unable to access camera" error

**Causes:**
- Camera permissions denied
- HTTPS required (camera API only works on HTTPS)
- Browser not supported

**Solutions:**
```javascript
// Better error handling
html5QrCode.start(...)
    .catch(err => {
        if (err.name === 'NotAllowedError') {
            alert('Please grant camera permission to scan QR code');
        } else if (err.name === 'NotFoundError') {
            alert('No camera found on your device');
        } else {
            alert('Unable to access camera: ' + err.message);
        }
    });
```

### Issue 2: QR Code Not Scanning

**Symptoms:** Camera works but doesn't detect QR

**Causes:**
- QR code too small/blurry
- Poor lighting
- Camera focus issues

**Solutions:**
- Increase QR code size: `width: 300, height: 300`
- Better lighting in classroom
- Use high error correction: `QRCode.CorrectLevel.H`
- Increase FPS: `fps: 15`

### Issue 3: Timer Drift

**Symptoms:** Timer doesn't match actual time remaining

**Cause:** JavaScript timer can drift due to browser throttling

**Solution:**
```javascript
// Calculate from server time instead
function updateTimer() {
    const now = Date.now();
    const expiresAt = new Date(session.expires_at).getTime();
    const remaining = Math.max(0, Math.floor((expiresAt - now) / 1000));
    
    // Display remaining time...
}
```

### Issue 4: Students Marked as Absent When They Scanned

**Cause:** Network delay or session expired before scan processed

**Solutions:**
- Grace period: Make expiry 10:30 instead of 10:00
- Better error messages to student
- Allow manual correction by teacher

---

## 🚀 Future Enhancements

### Phase 2 Features:
- **Geolocation Verification** - Ensure students are physically in classroom
- **Face Recognition** - Verify student identity while scanning
- **Export to Excel** - Download attendance reports as spreadsheet
- **Email Notifications** - Alert students when marked absent
- **Attendance Analytics** - Charts showing trends over semester
- **Mobile App** - Native Android/iOS app for better camera performance

### Phase 3 Features:
- **Automatic Scheduling** - Generate QR at scheduled class times
- **Proximity Detection** - Use Bluetooth/WiFi to verify location
- **Parent Portal** - Parents can view child's attendance
- **Integration with LMS** - Sync with Canvas, Moodle, etc.

---

## 📝 API Summary

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `/courses/{id}/attendance/generate-qr` | POST | Teacher | Generate new QR session |
| `/attendance/session/{id}/status` | GET | Teacher | Get live updates |
| `/attendance/session/{id}/close` | POST | Teacher | Close session manually |
| `/attendance/mark` | POST | Student | Mark attendance by scanning |
| `/courses/{id}/attendance/active` | GET | Student | Check for active session |
| `/courses/{id}/attendance/data` | GET | Student | Get calendar data |

---

## 🎓 Learning Resources

### QR Code Libraries:
- **QRCode.js** - https://davidshimjs.github.io/qrcodejs/
- **Html5-QRCode** - https://github.com/mebjas/html5-qrcode

### Camera Access:
- **MediaDevices API** - https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices
- **getUserMedia()** - https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia

### Real-time Updates:
- **Polling vs WebSockets** - https://ably.com/topic/websockets-vs-http-polling
- **Server-Sent Events** - https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events

---

*Last Updated: October 25, 2025*  
*System Version: 1.0*  
*Laravel Version: 11.x*
