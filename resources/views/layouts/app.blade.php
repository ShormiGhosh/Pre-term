<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pre-term System</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } 
/*  */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #100f21;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(28, 26, 54, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(64, 26, 117, 0.3);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-content {
            max-width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-user {
            font-size: 1.1rem;
            font-weight: 500;
            color: #F1F5FB;
            padding: 0.5rem 1rem;
            background: rgba(64, 26, 117, 0.3);
            border-radius: 8px;
            border: 1px solid rgba(64, 26, 117, 0.5);
        }

        .navbar-right {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .navbar-links {
            display: flex;
            gap: 0.5rem;
        }

        .navbar-links a, .navbar-links button {
            color: #C1CEE5;
            text-decoration: none;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.95rem;
            font-weight: 500;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
        }

        .navbar-links a:hover, .navbar-links button:hover {
            background: rgba(64, 26, 117, 0.4);
            border-color: rgba(64, 26, 117, 0.6);
            color: #F1F5FB;
            transform: translateY(-1px);
        }

        .navbar-links a.active {
            background: rgba(64, 26, 117, 0.5);
            border-color: rgba(64, 26, 117, 0.7);
            color: #F1F5FB;
        }

        .btn-logout {
            background: rgba(45, 26, 31, 0.5);
            color: #F9896B;
            border: 1px solid rgba(248, 137, 107, 0.4);
        }

        .btn-logout:hover {
            background: rgba(45, 26, 31, 0.8);
            border-color: #F9896B;
            color: #F9896B;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .card {
            background: #1c1a36;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }

        .card-title {
            font-size: 2rem;
            color: #F1F5FB;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #302e4a;
            color: #C1CEE5;
            border: 1px solid #401a75;
        }

        .alert-error {
            background-color: #F9896B;
            color: #FFFFFF;
            border: 1px solid #F9896B;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #C1CEE5;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #302e4a;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
            background-color: #100f21;
            color: #F1F5FB;
        }

        .form-input:focus {
            outline: none;
            border-color: #401a75;
        }

        .form-error {
            color: #F9896B;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #302e4a;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background-color: #401a75;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(64, 26, 117, 0.5);
        }

        .btn-danger {
            background-color: #401a75;
            color: #FFFFFF;
        }

        .btn-danger:hover {
            background-color: #302e4a;
        }

        .text-center {
            text-align: center;
        }

        .link {
            color: #C1CEE5;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
            color: #401a75;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .dashboard-card {
            background: #1c1a36;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .user-info {
            background-color: #302e4a;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .user-info p {
            margin: 0.5rem 0;
            color: #C1CEE5;
        }
    </style>
</head>
<body>
    @if(session('user_type'))
    <nav class="navbar">
        <div class="navbar-content">
            <!-- Left Side: User Name -->
            <div class="navbar-left">
                <span class="navbar-user">{{ session('user_name') }}</span>
            </div>

            <!-- Right Side: Navigation Links -->
            <div class="navbar-right">
                <div class="navbar-links">
                    @if(session('user_type') === 'teacher')
                        <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('teacher.profile') }}" class="{{ request()->routeIs('teacher.profile*') ? 'active' : '' }}">Profile</a>
                        <form action="{{ route('teacher.logout') }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">Dashboard</a>
                        <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile*') ? 'active' : '' }}">Profile</a>
                        <form action="{{ route('student.logout') }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout">Logout</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="container">
        @yield('content')
    </div>
</body>
</html>

