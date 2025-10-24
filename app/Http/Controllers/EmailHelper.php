<?php

namespace App\Http\Controllers;

use App\Models\VerificationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailHelper
{
    
    public static function sendVerificationEmail($email, $code, $name, $type = 'signup')
    {
        $subject = $type === 'signup' ? 'Verify Your Email - PreTerm' : 'Password Reset Code - PreTerm';
        $message = self::buildEmailMessage($name, $code, $type);

        try {
            // Using Laravel's Mail facade
            Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)
                     ->subject($subject);
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Build email message content
     */
    private static function buildEmailMessage($name, $code, $type)
    {
        if ($type === 'signup') {
            return "Hello {$name},\n\n" .
                   "Thank you for signing up for PreTerm!\n\n" .
                   "Your verification code is: {$code}\n\n" .
                   "This code will expire in 15 minutes.\n\n" .
                   "If you didn't request this, please ignore this email.\n\n" .
                   "Best regards,\n" .
                   "PreTerm Team";
        } else {
            return "Hello {$name},\n\n" .
                   "You requested to reset your password for PreTerm.\n\n" .
                   "Your password reset code is: {$code}\n\n" .
                   "This code will expire in 15 minutes.\n\n" .
                   "If you didn't request this, please ignore this email and your password will remain unchanged.\n\n" .
                   "Best regards,\n" .
                   "PreTerm Team";
        }
    }
}
