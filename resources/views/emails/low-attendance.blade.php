<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Attendance Alert</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .warning-icon {
            font-size: 32px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #401a75;
            margin-bottom: 20px;
        }
        .alert-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert-box h2 {
            color: #ef4444;
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #401a75;
        }
        .stat-value.danger {
            color: #ef4444;
        }
        .course-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .course-info h3 {
            margin: 0 0 10px 0;
            color: #401a75;
        }
        .course-details {
            color: #6b7280;
        }
        .recommendations {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .recommendations h3 {
            color: #f59e0b;
            margin: 0 0 15px 0;
        }
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
        }
        .recommendations li {
            margin-bottom: 10px;
            color: #78350f;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #401a75 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <span class="warning-icon">⚠️</span>
                Low Attendance Alert
            </h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Dear {{ $studentName }},</p>

            <div class="alert-box">
                <h2>Attendance Below Required Threshold</h2>
                <p style="margin: 0;">
                    This is an important notice regarding your attendance in the following course. 
                    Your current attendance rate has fallen below the minimum required percentage of 60%.
                </p>
            </div>

            <!-- Course Information -->
            <div class="course-info">
                <h3>📚 Course Details</h3>
                <div class="course-details">
                    <strong>Course Code:</strong> {{ $course->course_code }}<br>
                    <strong>Course Title:</strong> {{ $course->course_title }}<br>
                    <strong>Teacher:</strong> {{ $course->teacher->name }}
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Attendance Rate</div>
                    <div class="stat-value danger">{{ number_format($attendanceRate, 1) }}%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Sessions Present</div>
                    <div class="stat-value">{{ $presentCount }}/{{ $totalSessions }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Sessions Missed</div>
                    <div class="stat-value danger">{{ $totalSessions - $presentCount }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Required Rate</div>
                    <div class="stat-value">≥ 60%</div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="recommendations">
                <h3>💡 Recommendations</h3>
                <ul>
                    <li><strong>Attend all upcoming classes</strong> to improve your attendance rate</li>
                    <li><strong>Contact your teacher</strong> if you're facing any difficulties</li>
                    <li><strong>Stay updated</strong> with class schedules and attendance sessions</li>
                    <li><strong>Set reminders</strong> for class times to avoid missing sessions</li>
                </ul>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                <strong>Important:</strong> Maintaining good attendance is crucial for your academic progress. 
                Continued low attendance may affect your eligibility for examinations and final assessments.
            </p>

            <center>
                <a href="{{ config('app.url') }}" class="cta-button">View Dashboard</a>
            </center>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                This is an automated notification from your academic institution.
            </p>
            <p style="margin: 0; font-size: 12px;">
                © {{ date('Y') }} Pre-term. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
