# Email Verification & Password Reset - Implementation Guide

## ✅ Features Implemented

### 1. **Email Verification for Student Signup**
- When a student signs up, a 6-digit verification code is generated
- Code is sent to their email
- Email must be verified before login
- Code expires in 15 minutes
- Can resend verification code

### 2. **Show/Hide Password Toggle**
- Added to all login and signup forms (both teacher and student)
- Eye icon (👁️) to show password
- Closed eye (🙈) to hide password
- Works on password and confirm password fields

### 3. **Forgot Password with Email Verification**
- Student can request password reset
- 6-digit code sent to email
- Code verification required
- Set new password after verification
- Code expires in 15 minutes

## 📋 Database Changes

### New Table: `verification_codes`
```
- id
- email (email address)
- code (6-digit verification code)
- type (signup or reset)
- expires_at (code expiration time)
- is_used (track if code was used)
- timestamps
```

### Updated Table: `students`
```
- email_verified (boolean, default false)
```

## 🔄 User Flow

### Student Signup Flow:
1. Student fills signup form
2. Account created with `email_verified = false`
3. Verification code generated and sent to email
4. Student redirected to verification page
5. Student enters 6-digit code
6. Code verified → `email_verified = true`
7. Student logged in and redirected to dashboard

### Student Login Flow (Unverified):
1. Student enters credentials
2. Password correct but email not verified
3. New verification code generated and sent
4. Redirected to verification page
5. Must verify email to proceed

### Forgot Password Flow:
1. Student clicks "Forgot Password" on login page
2. Enters email address
3. Reset code sent to email
4. Student enters 6-digit code
5. Code verified
6. Student sets new password
7. Redirected to login with success message

## 📧 Email Configuration

### For Development (Current Setup):
The `.env` file is set to `MAIL_MAILER=log`, which means emails are logged to `storage/logs/laravel.log` instead of being sent.

**To see the verification codes during development:**
- Codes are displayed on the screen after signup/reset (for testing)
- Check `storage/logs/laravel.log` for email content

### For Production (Gmail SMTP):
Update `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Pre-term System"
```

**Important for Gmail:**
1. Enable 2-factor authentication on your Google account
2. Generate an "App Password" from your Google Account settings
3. Use the App Password (not your regular password) in `MAIL_PASSWORD`

### For Production (Other SMTP Services):
You can also use:
- **Mailtrap** (for testing)
- **SendGrid**
- **Mailgun**
- **Amazon SES**

## 🎨 UI Features

### Show/Hide Password:
- Click the eye icon (👁️) to show password as plain text
- Icon changes to 🙈 when password is visible
- Click again to hide password
- Works on all password fields

### Verification Code Input:
- Large, centered input field
- Letter-spacing for better readability
- Auto-formats to accept only numbers
- Maximum 6 digits
- Auto-focus on page load

## 🔒 Security Features

1. **Code Expiration**: All codes expire after 15 minutes
2. **One-time Use**: Codes can only be used once (`is_used` flag)
3. **Password Hashing**: Passwords automatically hashed using bcrypt
4. **Session-based**: Verification process uses secure sessions
5. **Validation**: Email format validation for KUET addresses

## 📁 New Files Created

### Models:
- `app/Models/VerificationCode.php`

### Controllers:
- `app/Http/Controllers/EmailHelper.php`
- Updated `app/Http/Controllers/StudentAuthController.php`

### Migrations:
- `database/migrations/2024_10_24_000003_create_verification_codes_table.php`
- `database/migrations/2024_10_24_000004_add_email_verified_to_students_table.php`

### Views:
- `resources/views/student/verify.blade.php` - Email verification page
- `resources/views/student/forgot-password.blade.php` - Request reset code
- `resources/views/student/reset-verify.blade.php` - Verify reset code
- `resources/views/student/reset-password.blade.php` - Set new password

### Updated Views:
- All login/signup forms now have password toggle

## 🧪 Testing the Features

### Test Email Verification:
1. Visit `http://127.0.0.1:8000/student/signup`
2. Fill in the form with valid data
3. Submit the form
4. You'll see the verification code on screen (for development)
5. Enter the 6-digit code
6. You'll be logged in and redirected to dashboard

### Test Forgot Password:
1. Visit `http://127.0.0.1:8000/student/login`
2. Click "Forgot Password?"
3. Enter your email
4. You'll see the reset code on screen
5. Enter the 6-digit code
6. Set your new password
7. Login with new password

### Test Password Toggle:
1. Visit any login or signup page
2. Start typing in password field
3. Click the eye icon (👁️)
4. Password becomes visible
5. Click again to hide

## 🚀 Next Steps

To send real emails:
1. Set up Gmail App Password or SMTP service
2. Update `.env` file with mail settings
3. Run `php artisan config:clear`
4. Test email sending

## 💡 Code Explanation

### How Verification Works:
```php
// Generate random 6-digit code
$code = VerificationCode::generateCode(); // e.g., "123456"

// Store in database with expiration
VerificationCode::create([
    'email' => $email,
    'code' => $code,
    'type' => 'signup', // or 'reset'
    'expires_at' => now()->addMinutes(15),
]);

// Send email (logs to file in development)
EmailHelper::sendVerificationEmail($email, $code, $name, 'signup');
```

### How Password Toggle Works:
```javascript
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    
    // Change input type between 'password' and 'text'
    if (field.type === 'password') {
        field.type = 'text';  // Show password
        button.textContent = '🙈';
    } else {
        field.type = 'password';  // Hide password
        button.textContent = '👁️';
    }
}
```

## 📝 Notes

- Teachers do NOT need email verification (only students)
- Verification codes are stored in database, not sessions
- Old/expired codes remain in database for audit trail
- Session data is used to track verification flow
- For production, consider adding rate limiting to prevent abuse

## 🛠️ Troubleshooting

**Email not sending?**
- Check `storage/logs/laravel.log` for errors
- Verify MAIL settings in `.env`
- Run `php artisan config:clear`

**Code not working?**
- Check if code expired (15 minutes)
- Verify code wasn't already used
- Check database `verification_codes` table

**Session errors?**
- Make sure session migration ran (`sessions` table exists)
- Clear browser cookies
- Run `php artisan session:table` if needed
