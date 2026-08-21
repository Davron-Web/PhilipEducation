<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPlatform - Learning Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0a0a12] text-white min-h-screen">
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-violet-600 to-purple-600 bg-clip-text text-transparent">
            EduPlatform
        </h1>
        <p class="text-xl text-gray-400 mb-8">Learning Management System</p>

        <div class="space-x-4">
            <a href="{{ route('login') }}" class="px-6 py-3 bg-gradient-to-r from-violet-700 to-purple-700 rounded-xl font-semibold hover:from-violet-600 hover:to-purple-600 transition-all">
                Login
            </a>
            <a href="{{ route('register') }}" class="px-6 py-3 bg-white/5 border border-purple-900/30 rounded-xl font-semibold hover:bg-white/10 transition-all">
                Register
            </a>
        </div>
    </div>
</div>
</body>
</html>
