<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PreTerm</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } 
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
            background: transparent;
            border: none;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #C1CEE5;
            text-decoration: none;
            padding: 0.625rem 1.25rem;
            transition: color 0.3s;
            font-size: 0.95rem;
            font-weight: 500;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: inherit;
            position: relative;
        }

        .navbar-links a::after, .navbar-links button::after {
            content: '';
            position: absolute;
            bottom: 0.3rem;
            left: 1.25rem;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #401a75, #5e2a9e);
            transition: width 0.3s ease-out;
        }

        .navbar-links a:hover, .navbar-links button:hover {
            color: #F1F5FB;
        }

        .navbar-links a:hover::after, .navbar-links button:hover::after {
            width: calc(100% - 2.5rem);
        }

        .navbar-links a.active {
            color: #F1F5FB;
        }

        .navbar-links a.active::after {
            width: calc(100% - 2.5rem);
            background: linear-gradient(90deg, #5e2a9e, #401a75);
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

        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(64, 26, 117, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }

        .notification-bell:hover {
            background: rgba(64, 26, 117, 0.5);
            transform: scale(1.05);
        }

        .notification-bell .material-symbols-outlined {
            color: #C1CEE5;
            font-size: 1.4rem;
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, #F9896B, #ff6b4a);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(249, 137, 107, 0.5);
        }

        .notification-dropdown {
            position: absolute;
            top: 55px;
            right: 0;
            width: 360px;
            max-height: 500px;
            background: #1c1a36;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            border: 1px solid #302e4a;
            overflow: hidden;
            z-index: 1000;
            display: none;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            background: linear-gradient(135deg, #302e4a, #401a75);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #302e4a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h3 {
            margin: 0;
            color: #F1F5FB;
            font-size: 1rem;
            font-weight: 600;
        }

        .mark-all-read {
            background: none;
            border: none;
            color: #C1CEE5;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .mark-all-read:hover {
            background: rgba(193, 206, 229, 0.1);
            color: #F1F5FB;
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-list::-webkit-scrollbar {
            width: 6px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: #100f21;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #302e4a;
            border-radius: 3px;
        }

        .notification-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #302e4a;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: start;
            gap: 0.8rem;
        }

        .notification-item:hover {
            background: rgba(64, 26, 117, 0.1);
        }

        .notification-item.unread {
            background: rgba(64, 26, 117, 0.15);
        }

        .notification-icon {
            font-size: 1.5rem;
            min-width: 30px;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            color: #F1F5FB;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .notification-message {
            color: #C1CEE5;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }

        .notification-time {
            color: #8B9DC3;
            font-size: 0.75rem;
        }

        .notification-empty {
            padding: 3rem 2rem;
            text-align: center;
            color: #8B9DC3;
        }

        .notification-empty .material-symbols-outlined {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    @if(session('user_type'))
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-left">
                <span class="navbar-user">{{ session('user_name') }}</span>
            </div>

            <div class="navbar-right">
                <div class="navbar-links">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                        <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">home</span>
                        Home
                    </a>
                    @if(session('user_type') === 'teacher')
                        <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">book</span>
                            Dashboard
                        </a>
                        <a href="{{ route('teacher.profile') }}" class="{{ request()->routeIs('teacher.profile*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">person</span>
                            Profile
                        </a>
                        <form action="{{ route('teacher.logout') }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout">
                                <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">logout</span>
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">book</span>
                            Dashboard
                        </a>
                        <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">person</span>
                            Profile
                        </a>
                        <form action="{{ route('student.logout') }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout">
                                <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.25rem;">logout</span>
                                Logout
                            </button>
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

