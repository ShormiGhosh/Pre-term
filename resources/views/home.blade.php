<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-term Attendance System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('{{ asset('images/kuet_bg.webp') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        /* Dark overlay for better text visibility */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        .home-container {
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
        }

        .home-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .home-subtitle {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .portal-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .portal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }

        .portal-card h2 {
            color: #667eea;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .portal-card p {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .portal-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .features {
            margin-top: 3rem;
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .features h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .features ul {
            list-style: none;
            padding: 0;
        }

        .features li {
            padding: 0.5rem 0;
            opacity: 0.9;
        }

        .features li:before {
            content: "✓ ";
            font-weight: bold;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="home-container">
        <h1 class="home-title">Pre-term Attendance System</h1>
        <p class="home-subtitle">Track attendance, CT marks, and class performance - All in one place</p>

        <div class="cards-container">
            <div class="portal-card">
                <h2>👨‍🏫 Teacher Portal</h2>
                <p>Manage student records, track attendance, schedule CTs, and record marks.</p>
                <div class="portal-buttons">
                    <a href="{{ route('teacher.login') }}" class="btn btn-primary">Login as Teacher</a>
                    <a href="{{ route('teacher.signup') }}" class="btn btn-secondary">Sign Up as Teacher</a>
                </div>
            </div>

            <div class="portal-card">
                <h2>👨‍🎓 Student Portal</h2>
                <p>View your attendance, check CT schedules, track marks, and monitor eligibility for finals.</p>
                <div class="portal-buttons">
                    <a href="{{ route('student.login') }}" class="btn btn-primary">Login as Student</a>
                    <a href="{{ route('student.signup') }}" class="btn btn-secondary">Sign Up as Student</a>
                </div>
            </div>
        </div>

        <div class="features">
            <h3>System Features</h3>
            <ul>
                <li>Attendance tracking with 60% eligibility alerts</li>
                <li>CT schedule and marks management</li>
                <li>Class performance records (20 marks)</li>
                <li>Teacher-wise mark distribution</li>
                <li>Real-time eligibility status for term finals</li>
            </ul>
        </div>
    </div>
</body>
</html>
