# Email Setup Guide - Send Real Emails

## Gmail SMTP Setup (Recommended)

### Step 1: Enable 2-Factor Authentication

1. Go to your Google Account: https://myaccount.google.com/
2. Click on **Security** (left sidebar)
3. Find **2-Step Verification**
4. Click **Get Started** and follow the steps

### Step 2: Generate App Password

1. After enabling 2FA, go back to **Security**
2. Find **App passwords** (under 2-Step Verification section)
3. Or visit directly: https://myaccount.google.com/apppasswords
4. You may need to sign in again
5. Under "Select app" → Choose **Mail**
6. Under "Select device" → Choose **Windows Computer** (or Other)
7. Click **Generate**
8. **Copy the 16-character password** (it will look like: `abcd efgh ijkl mnop`)
9. Click **Done**

### Step 3: Update .env File

Open `c:\Users\Shormi\pre-term\.env` and update these lines:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Pre-term System"
```

**Replace:**
- `your-gmail@gmail.com` with your actual Gmail address
- `abcdefghijklmnop` with the 16-character app password (remove spaces)

**Example:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=shormi2024@gmail.com
MAIL_PASSWORD=xyzw abcd efgh ijkl
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=shormi2024@gmail.com
MAIL_FROM_NAME="Pre-term System"
```

### Step 4: Clear Laravel Cache

Run in PowerShell:

```powershell
cd c:\Users\Shormi\pre-term
php artisan config:clear
php artisan cache:clear
```

### Step 5: Test Email Sending

1. **Update the test route**: 
   - Open `routes/web.php`
   - Find the `/test-email` route
   - Change `test@stud.kuet.ac.bd` to your actual email address

2. **Visit the test URL**:
   ```
   http://127.0.0.1:8000/test-email
   ```

3. **Check your email inbox** (and spam folder!)

### Step 6: Remove Test Email Route (After Testing)

Once email is working, remove the test route from `routes/web.php`

---

## Alternative: Using Mailtrap (For Testing)

If you want to test emails without sending real emails:

1. Sign up at https://mailtrap.io (Free)
2. Get your SMTP credentials from the inbox
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@preterm.com
MAIL_FROM_NAME="Pre-term System"
```

All emails will be caught by Mailtrap and you can view them in their web interface.

---

## Troubleshooting

### Error: "Connection could not be established"

**Solution 1**: Check your Gmail credentials
- Make sure you're using the **App Password**, not your regular Gmail password
- Remove any spaces from the app password

**Solution 2**: Check firewall
- Make sure port 587 is not blocked by your firewall

**Solution 3**: Try port 465 with SSL
```env
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

### Error: "Authentication failed"

- Double-check your Gmail address
- Regenerate the App Password
- Make sure 2FA is enabled

### Emails going to Spam

- This is normal for development
- In production, use a proper email service or configure SPF/DKIM records

### Error: "Failed to authenticate on SMTP server"

Run these commands:
```powershell
php artisan config:clear
php artisan cache:clear
```

Then restart your development server:
```powershell
php artisan serve
```

---

## Production Email Services (Future)

For production, consider these services:

### 1. SendGrid (Free tier: 100 emails/day)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

### 2. Mailgun (Free tier: 5000 emails/month)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-api-key
```

### 3. Amazon SES (Very cheap, $0.10/1000 emails)
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
```

---

## Current Email Template

The emails currently send plain text. They look like:

```
Hello [Name],

Thank you for signing up for the Pre-term Attendance System!

Your verification code is: 123456

This code will expire in 15 minutes.

If you didn't request this, please ignore this email.

Best regards,
Pre-term System Team
```

### To Customize Email Content

Edit `app/Http/Controllers/EmailHelper.php` → `buildEmailMessage()` method

---

## Security Notes

⚠️ **Important:**
- Never commit your `.env` file to Git
- Never share your App Password
- Regenerate App Password if it's compromised
- Use environment-specific email services in production

---

## Summary

✅ **For Development**: Use Gmail with App Password  
✅ **For Testing**: Use Mailtrap  
✅ **For Production**: Use SendGrid, Mailgun, or Amazon SES

Once configured, your signup and password reset emails will be sent automatically!
