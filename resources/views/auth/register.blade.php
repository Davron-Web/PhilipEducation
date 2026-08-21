<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EduPlatform</title>

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

        input[type="text"],
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

        input[type="text"]:focus,
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
        <p>Create your account</p>
    </div>

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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                placeholder="Enter your name"
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
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

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                placeholder="Confirm password"
            >
        </div>

        <div class="checkbox-group">
            <input
                type="checkbox"
                id="terms"
                name="terms"
                required
            >
            <label for="terms">I agree to the Terms and Conditions</label>
        </div>

        <button type="submit" class="btn">Sign Up</button>
    </form>

    <div class="links">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </div>
</div>
</body>
</html>
