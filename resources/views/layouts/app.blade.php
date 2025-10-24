<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pre-term System</title>
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
            background-color: #302e4a;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #FFFFFF;
            text-decoration: none;
        }

        .navbar-links {
            display: flex;
            gap: 1rem;
        }

        .navbar-links a {
            color: #F1F5FB;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar-links a:hover {
            background-color: #401a75;
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
            <a href="/" class="navbar-brand">Pre-term System</a>
            <div class="navbar-links">
                <span style="color: #F1F5FB; font-weight: 500;">{{ session('user_name') }}</span>
                <form action="{{ session('user_type') === 'teacher' ? route('teacher.logout') : route('student.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #F9896B; cursor: pointer; font-size: 1rem; padding: 0.5rem 1rem;">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endif

    <div class="container">
        @yield('content')
    </div>
</body>
</html>

