# Pre-term Attendance System - Setup Guide

## What We've Built

I've implemented a complete authentication system for your Laravel Pre-term project with:

### 1. **Database Migrations** (Database CRUD)
- `teachers` table with fields: id, name, email, password, department, designation
- `students` table with fields: id, name, email, password, roll_number, department, year, semester
- Both tables have timestamps (created_at, updated_at) for tracking

### 2. **Models**
- `Teacher` model - extends Laravel's Authenticatable for authentication features
- `Student` model - extends Laravel's Authenticatable for authentication features
- Both use password hashing automatically

### 3. **Controllers**
- `TeacherAuthController` - handles teacher signup, login, logout
- `StudentAuthController` - handles student signup, login, logout
- Email validation enforces KUET format:
  - Teachers: `teachername@dept.kuet.ac.bd`
  - Students: `surnameRoll@stud.kuet.ac.bd`

### 4. **Middleware** (Session Protection)
- `TeacherAuth` - protects teacher-only routes
- `StudentAuth` - protects student-only routes
- Both check session data to verify user is logged in

### 5. **Sessions & Cookies**
- User info stored in session after login:
  - `user_id`, `user_type`, `user_name`, `user_email`
- Session cleared on logout
- Middleware checks session before allowing access to protected routes

### 6. **Views (Blade Templates)**
- Home page with portals for both teachers and students
- Teacher: signup, login, dashboard
- Student: signup, login, dashboard
- Responsive design with gradient styling

### 7. **Routes**
- Public routes: home, login pages, signup pages
- Protected routes: dashboards (require authentication)
- Middleware applied to protect dashboard routes

## How to Run the Project

### Step 1: Set Up Database
1. Copy `.env.example` to `.env`:
   ```powershell
   Copy-Item .env.example .env
   ```

2. Open `.env` file and configure your database:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=preterm_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. Create the database in MySQL:
   ```sql
   CREATE DATABASE preterm_db;
   ```

### Step 2: Generate Application Key
```powershell
php artisan key:generate
```

### Step 3: Run Migrations (Create Tables)
```powershell
php artisan migrate
```

This will create the `teachers` and `students` tables in your database.

### Step 4: Start Development Server
```powershell
php artisan serve
```

Visit: `http://localhost:8000`

## How It Works

### Authentication Flow:

1. **Signup Process:**
   - User fills signup form (teacher or student)
   - Email format is validated (KUET format)
   - Password is hashed automatically
   - User record created in database
   - Session created with user info
   - Redirected to dashboard

2. **Login Process:**
   - User enters email and password
   - System finds user by email
   - Password verified using Hash::check()
   - Session created with user info
   - Redirected to dashboard

3. **Session Management:**
   - User info stored in session (not cookies directly)
   - Laravel handles session storage (file/database/redis)
   - Session checked by middleware on each request

4. **Logout Process:**
   - Session flushed (all data cleared)
   - User redirected to login page

### Middleware Protection:

Routes like `/teacher/dashboard` are protected:
```php
Route::middleware(['teacher.auth'])->group(function () {
    Route::get('/teacher/dashboard', ...);
});
```

Middleware checks:
- Is session variable `user_id` set?
- Is `user_type` equal to 'teacher'?
- If no, redirect to login
- If yes, allow access

## Laravel Concepts Explained

### 1. **MVC Pattern**
- **Model** (Teacher.php, Student.php): Interact with database tables
- **View** (signup.blade.php, login.blade.php): HTML templates
- **Controller** (TeacherAuthController.php): Handle logic and connect model+view

### 2. **Migrations** (Database CRUD - Create)
- PHP classes that define database structure
- Version control for your database
- Commands:
  - `php artisan migrate` - Run migrations
  - `php artisan migrate:rollback` - Undo last migration
  - `php artisan migrate:fresh` - Drop all tables and re-run

### 3. **Eloquent ORM**
- Laravel's database abstraction
- Models represent database tables
- Create records: `Teacher::create([...])`
- Find records: `Teacher::where('email', $email)->first()`
- Update records: `$teacher->update([...])`
- Delete records: `$teacher->delete()`

### 4. **Blade Templating**
- `@extends('layouts.app')` - Use a layout
- `@section('content')` - Define section content
- `{{ $variable }}` - Echo variable (escaped)
- `@if`, `@foreach`, `@error` - Control structures

### 5. **Routes**
- `Route::get('/path', [Controller::class, 'method'])` - Define endpoints
- `->name('route.name')` - Give routes names
- `route('route.name')` - Generate URLs by name
- `Route::middleware([...])` - Apply middleware

### 6. **Validation**
- `$request->validate([...])` - Validate input
- Rules: `required`, `email`, `unique:table`, `min:6`, `confirmed`
- Errors automatically sent back to form
- `@error('field')` shows validation errors

### 7. **Sessions**
- `session(['key' => 'value'])` - Store in session
- `session('key')` - Retrieve from session
- `session()->has('key')` - Check if exists
- `session()->flush()` - Clear all session data

## Next Steps

Your authentication system is complete! Here's what you can build next:

1. **Courses Management**
   - Create courses table (code, name, credit, semester)
   - Assign courses to teachers
   - Enroll students in courses

2. **Attendance System**
   - Track daily attendance
   - Calculate attendance percentage
   - Alert if below 60%

3. **CT Schedule & Marks**
   - Create CT schedules
   - Record CT marks (60 marks total)
   - View marks by student/course

4. **Class Performance**
   - Record assignment marks
   - Track participation
   - 20 marks allocation

Let me know which feature you'd like to implement next!
