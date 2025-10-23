# Pre-term System - Quick Reference

## URLs

### Development Server
- **Home Page**: http://127.0.0.1:8000
- **Teacher Login**: http://127.0.0.1:8000/teacher/login
- **Teacher Signup**: http://127.0.0.1:8000/teacher/signup
- **Student Login**: http://127.0.0.1:8000/student/login
- **Student Signup**: http://127.0.0.1:8000/student/signup

## Email Formats (Validation Required)

### Teacher Email
- Format: `teachername@dept.kuet.ac.bd`
- Examples:
  - `johndoe@cse.kuet.ac.bd`
  - `janesmith@eee.kuet.ac.bd`
  - `robertbrown@me.kuet.ac.bd`

### Student Email
- Format: `surnameRoll@stud.kuet.ac.bd`
- Examples:
  - `smith2103001@stud.kuet.ac.bd`
  - `khan2103025@stud.kuet.ac.bd`
  - `ahmed2103050@stud.kuet.ac.bd`

## Useful Laravel Commands

```powershell
# Start development server
php artisan serve

# Run migrations (create tables)
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Drop all tables and re-run migrations
php artisan migrate:fresh

# Create a new migration
php artisan make:migration create_table_name

# Create a new model
php artisan make:model ModelName

# Create a new controller
php artisan make:controller ControllerName

# Create a new middleware
php artisan make:middleware MiddlewareName

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# View all routes
php artisan route:list
```

## Database Tables

### teachers
- id (primary key)
- name
- email (unique)
- password (hashed)
- department
- designation (nullable)
- created_at
- updated_at

### students
- id (primary key)
- name
- email (unique)
- password (hashed)
- roll_number (unique)
- department
- year
- semester
- created_at
- updated_at

## Session Variables

After login, these are stored in session:
- `user_id` - The logged in user's ID
- `user_type` - Either 'teacher' or 'student'
- `user_name` - User's full name
- `user_email` - User's email

Access in blade: `{{ session('user_name') }}`
Access in controller: `session('user_name')`

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TeacherAuthController.php
│   │   └── StudentAuthController.php
│   └── Middleware/
│       ├── TeacherAuth.php
│       └── StudentAuth.php
└── Models/
    ├── Teacher.php
    └── Student.php

resources/views/
├── layouts/
│   └── app.blade.php
├── teacher/
│   ├── signup.blade.php
│   ├── login.blade.php
│   └── dashboard.blade.php
├── student/
│   ├── signup.blade.php
│   ├── login.blade.php
│   └── dashboard.blade.php
└── home.blade.php

database/migrations/
├── 2024_10_23_000001_create_teachers_table.php
└── 2024_10_23_000002_create_students_table.php

routes/
└── web.php
```

## Testing the Application

1. Visit http://127.0.0.1:8000
2. Click "Sign Up as Teacher" or "Sign Up as Student"
3. Fill in the form with proper email format
4. After signup, you'll be logged in automatically
5. Try logging out and logging back in
6. Try accessing dashboard without logging in (should redirect to login)

## Next Features to Implement

1. **Courses Module**
   - Create courses table
   - CRUD operations for courses
   - Assign teachers to courses
   - Enroll students in courses

2. **Attendance Module**
   - Daily attendance marking
   - Attendance percentage calculation
   - 60% eligibility check

3. **CT Module**
   - CT scheduling
   - CT marks recording (60 marks)
   - View marks by course

4. **Class Performance Module**
   - Assignment marks
   - Participation tracking
   - 20 marks allocation
