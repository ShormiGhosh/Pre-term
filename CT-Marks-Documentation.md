# 📊 CT Marks Management System - Complete Documentation

## 🎯 Overview

A comprehensive CT (Class Test) marks management system that allows:
- **Teachers**: View all students, enter/edit marks, calculate class averages, and download results as PDF
- **Students**: View their own marks with class averages for performance comparison
- **Real-time Updates**: Marks are saved via AJAX and instantly reflected across teacher and student views
- **Dynamic Columns**: CT columns automatically appear when teachers schedule new CTs

---

## 📁 Files Modified/Created

### Database
- `database/migrations/2024_10_25_create_ct_marks_table.php` - CT marks table migration

### Models
- `app/Models/CTMark.php` - CT marks model (added `protected $table = 'ct_marks';`)
- `app/Models/CTSchedule.php` - Already existed

### Controllers
- `app/Http/Controllers/CTScheduleController.php`:
  - `saveMarks()` - Save CT marks via AJAX
  - `downloadMarks()` - Generate PDF export

### Views
- `resources/views/courses/ct-marks-section.blade.php` - CT marks UI component
- `resources/views/courses/ct-marks-pdf.blade.php` - PDF export view
- `resources/views/courses/show.blade.php` - Updated to include CT marks section and tab persistence

### Routes
- `routes/web.php`:
  - POST `/courses/{courseId}/ct-marks/save`
  - GET `/courses/{courseId}/ct-marks/download`

---

## 🗄️ Database Schema

### ct_marks Table

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | BIGINT | No | Primary key |
| ct_schedule_id | BIGINT | No | Foreign key to ct_schedules |
| student_id | BIGINT | No | Foreign key to students |
| course_id | BIGINT | No | Foreign key to courses |
| marks_obtained | DECIMAL(5,2) | Yes | Marks scored (e.g., 18.75) |
| created_at | TIMESTAMP | Yes | Record creation time |
| updated_at | TIMESTAMP | Yes | Record update time |

**Constraints:**
- Unique constraint on `(ct_schedule_id, student_id)` - Prevents duplicate marks
- Foreign keys with CASCADE DELETE

---

## ✨ Key Features Implemented

### For Teachers:
✅ View all students with roll numbers  
✅ Dynamic CT columns (auto-generated from scheduled CTs)  
✅ Edit/Save marks with AJAX (no page reload)  
✅ Input validation (can't exceed total marks)  
✅ Real-time total calculation  
✅ Class average for each CT  
✅ Download marks as PDF  
✅ PDF shows: Roll, Name, CT marks, Total (removed individual averages)  
✅ Class average row in PDF  

### For Students:
✅ View their own marks in clean table  
✅ See class averages for comparison  
✅ Status badges (Graded/Pending)  
✅ Performance summary cards (Total, Percentage, Pass/Fail)  
✅ Responsive design  

### Tab Persistence:
✅ Remember active tab (Overview, CT Schedule, CT Marks, Attendance) when reloading page  
✅ Uses localStorage to save tab state per course  

---

## 🔧 Important Fixes Applied

1. **Table Name Issue**: Added `protected $table = 'ct_marks';` to CTMark model to prevent Laravel looking for `c_t_marks`

2. **Roll Number Field**: Changed from `student_roll` to `roll_number` throughout:
   - `ct-marks-section.blade.php`
   - `CTScheduleController.php` (downloadMarks method)

3. **PDF Customization**: 
   - Removed individual student average column
   - Kept total marks and class averages
   - Fixed roll number display

4. **Tab Persistence**: Added localStorage logic to remember active tab across page reloads

---

## 🔄 Data Flow

### Teacher Workflow:
1. Click "CT Marks" tab → Stays on tab after reload
2. Click "Edit Marks" → Enable all input fields
3. Enter marks → JavaScript validates (≤ total, ≥ 0)
4. Click "Save Marks" → AJAX POST to server
5. Server validates → `updateOrCreate()` in database
6. Success → Page reloads with updated data
7. Click "Download Result" → Opens PDF in new tab

### Student Workflow:
1. Click "CT Marks" tab → View own marks
2. See each CT with marks, class average, status
3. View performance summary (total, percentage, pass/fail)

---

## 🎨 UI Components

### Teacher View
- Header with Edit/Save/Cancel/Download buttons
- Table with:
  - Roll numbers (monospace font)
  - Student names
  - CT columns (dynamically generated)
  - Total column (highlighted)
  - Class average row (purple gradient)

### Student View
- Table showing:
  - CT name, date, total marks
  - Marks obtained (green highlight)
  - Class average (yellow)
  - Status badge (graded/pending)
- Performance summary cards

### PDF View
- Professional header with course info
- Clean table: Roll | Name | CT1 | CT2 | ... | Total
- Class average row at bottom
- Auto-triggers print dialog
- Print-optimized styling

---

## 🔒 Security

- ✅ CSRF token protection on all POST requests
- ✅ Teacher middleware ensures only course owner can edit
- ✅ Student can only view their own marks
- ✅ Server-side validation of all marks
- ✅ Unique constraint prevents duplicate entries

---

## 📊 Performance Optimizations

- Eager loading relationships to prevent N+1 queries
- `keyBy()` for O(1) lookups in student view
- Indexed foreign keys for faster queries

---

## 🐛 Common Issues & Solutions

**Issue**: "Table c_t_marks not found"  
**Solution**: Added `protected $table = 'ct_marks';` to CTMark model

**Issue**: Roll numbers not showing  
**Solution**: Changed `student_roll` to `roll_number` in queries

**Issue**: Page goes to Overview on reload  
**Solution**: Implemented localStorage to persist active tab

---

## 📝 Notes

- Marks support up to 2 decimal places (e.g., 18.75)
- PDF opens in new tab, triggers print dialog automatically
- Tab state is saved per course (course_1_activeTab, course_2_activeTab, etc.)
- Class averages exclude NULL marks (not graded students)
- Empty states shown when no students or CTs exist

---

*Last Updated: October 25, 2025*
