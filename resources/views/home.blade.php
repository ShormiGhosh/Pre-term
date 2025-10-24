<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-term Attendance System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('/images/kuet_bg.webp');
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
            background: rgba(16, 15, 33, 0.9);
            z-index: 0;
        }

        .navbar {
            background-color: #302e4a;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
            position: relative;
            z-index: 10;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #FFFFFF;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand .material-symbols-outlined {
            font-size: 2rem;
            color: #C1CEE5;
        }

        .home-container {
            text-align: center;
            color: #F1F5FB;
            position: relative;
            z-index: 1;
        }

        .logo {
            font-size: 6rem;
            margin-bottom: 0;
            color: #401a75;
        }

        .home-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            margin-top: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            color: #FFFFFF;
        }

        .home-subtitle {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.95;
            color: #C1CEE5;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .portal-card {
            background: #1c1a36;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.6);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .portal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(64, 26, 117, 0.6);
        }

        .portal-card h2 {
            color: #C1CEE5;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .portal-card p {
            color: #F1F5FB;
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
            background-color: #302e4a;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background-color: #401a75;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(64, 26, 117, 0.6);
        }

        .btn-secondary {
            background-color: #1c1a36;
            color: #C1CEE5;
            border: 1px solid #302e4a;
        }

        .btn-secondary:hover {
            background-color: #302e4a;
            color: #FFFFFF;
        }

        .features {
            margin-top: 3rem;
            background: rgba(48, 46, 74, 0.4);
            padding: 2rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(64, 26, 117, 0.3);
        }

        .features h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #FFFFFF;
        }

        .features ul {
            list-style: none;
            padding: 0;
        }

        .features li {
            padding: 0.5rem 0;
            opacity: 0.95;
            display: flex;
            align-items: center;
            color: #C1CEE5;
        }

        .features li .material-icons {
            margin-right: 0.5rem;
            font-size: 20px;
            color: #401a75;
        }
    </style>
</head>
<body>
    <div class="home-container">
        <div class="logo">
            <span class="material-symbols-outlined" style="font-size: 8rem;">groups_3</span>
        </div>
        <h1 class="home-title">
            <span style="color: #C1CEE5;">Pre</span><span style="color: #401a75;">Term</span>
        </h1>
        <p class="home-subtitle">Track attendance, CT marks, and class performance - All in one place</p>

        <div class="cards-container">
            <div class="portal-card">
                <h2><span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem; font-size: 2rem;">school</span>Teacher Portal</h2>
                <p>Manage student records, track attendance, schedule CTs, and record marks.</p>
                <div class="portal-buttons">
                    <a href="{{ route('teacher.login') }}" class="btn btn-primary">Login as Teacher</a>
                    <a href="{{ route('teacher.signup') }}" class="btn btn-secondary">Sign Up as Teacher</a>
                </div>
            </div>

            <div class="portal-card">
                <h2><span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem; font-size: 2rem;">person</span>Student Portal</h2>
                <p>View your attendance, check CT schedules, track marks, and monitor eligibility for finals.</p>
                <div class="portal-buttons">
                    <a href="{{ route('student.login') }}" class="btn btn-primary">Login as Student</a>
                    <a href="{{ route('student.signup') }}" class="btn btn-secondary">Sign Up as Student</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

