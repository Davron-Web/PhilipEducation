<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduPlatform</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Декоративный элемент (кривая линия) */
        .decoration {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            border-top: 60px solid #000;
            border-right: 60px solid #000;
            border-radius: 0 0 0 100px;
            opacity: 0.1;
        }

        .container {
            width: 100%;
            max-width: 400px;
            z-index: 1;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fff;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            cursor: pointer;
            accent-color: #7c3aed;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-size: 14px;
            color: #555;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #6d28d9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .links {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #666;
        }

        .links a {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #059669;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #7c3aed;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        /* Адаптивность */
        @media (max-width: 480px) {
            .decoration {
                width: 200px;
                height: 200px;
                border-width: 40px;
            }

            .logo h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
<div class="decoration"></div>

<div class="container">
    <div class="logo">
        <h1>EduPlatform</h1>
        <p>Welcome back</p>
    </div>

    @if(session('error'))
        <div class="error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="Enter your email"
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                placeholder="Enter password"
            >
        </div>

        <div class="forgot-password">
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        <div class="checkbox-group">
            <input
                type="checkbox"
                id="remember"
                name="remember"
            >
            <label for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn">Log in</button>
    </form>

    <div class="links">
        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
    </div>
</div>
</body>
</html>
