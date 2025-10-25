<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 28px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .ct-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
        }
        .ct-info h2 {
            margin: 0 0 20px 0;
            font-size: 24px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            opacity: 0.9;
        }
        .info-value {
            font-weight: 500;
        }
        .description {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .description p {
            margin: 0;
            color: #555;
        }
        .alert {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .alert p {
            margin: 0;
            color: #92400e;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        @media only screen and (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-value {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 CT Schedule Notification</h1>
        </div>

        <div class="greeting">
            Hello <strong>{{ $studentName }}</strong>,
        </div>

        <p>A new CT has been scheduled for your course. Please review the details below:</p>

        <div class="ct-info">
            <h2>{{ $ctSchedule->ct_name }}</h2>
            
            <div class="info-row">
                <span class="info-label">Course:</span>
                <span class="info-value">{{ $ctSchedule->course->course_code }} - {{ $ctSchedule->course->course_title }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Date & Time:</span>
                <span class="info-value">{{ $ctSchedule->ct_datetime->format('l, F j, Y \a\t g:i A') }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Total Marks:</span>
                <span class="info-value">{{ $ctSchedule->total_marks }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Instructor:</span>
                <span class="info-value">{{ $ctSchedule->course->teacher->name }}</span>
            </div>
        </div>

        @if($ctSchedule->description)
        <div class="description">
            <strong>Additional Information:</strong>
            <p>{{ $ctSchedule->description }}</p>
        </div>
        @endif

        <div class="alert">
            <p><strong>Reminder:</strong> Make sure to prepare well for this CT. Good luck!</p>
        </div>

        <div class="footer">
            <p>This is an automated notification from your Course Management System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
