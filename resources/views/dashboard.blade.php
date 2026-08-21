<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a12;
            color: #e4e4f0;
        }
    </style>
</head>
<body class="antialiased min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900">
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-4xl w-full">
        <div class="bg-[#14141f]/80 backdrop-blur-xl border border-purple-900/30 rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Admin Dashboard</h1>
                <p class="text-purple-400">Welcome back, {{ auth()->user()->name ?? 'Admin' }}!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-purple-950/30 border border-purple-800/30 rounded-xl p-6 text-center">
                    <p class="text-3xl font-bold text-violet-400 mb-2">150</p>
                    <p class="text-sm text-gray-400">Total Users</p>
                </div>
                <div class="bg-purple-950/30 border border-purple-800/30 rounded-xl p-6 text-center">
                    <p class="text-3xl font-bold text-violet-400 mb-2">50</p>
                    <p class="text-sm text-gray-400">Lessons</p>
                </div>
                <div class="bg-purple-950/30 border border-purple-800/30 rounded-xl p-6 text-center">
                    <p class="text-3xl font-bold text-violet-400 mb-2">247</p>
                    <p class="text-sm text-gray-400">Words</p>
                </div>
            </div>

            <div class="space-y-4">
                <a href="{{ route('admin.content.lessons.index') }}" class="block w-full px-6 py-3 bg-gradient-to-r from-violet-700 to-purple-700 text-white text-center font-semibold rounded-xl hover:from-violet-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-900/30">
                    Manage Lessons
                </a>
                <a href="{{ route('admin.user.users.index') }}" class="block w-full px-6 py-3 bg-white/5 border border-purple-900/30 text-white text-center font-semibold rounded-xl hover:bg-white/10 transition-all">
                    Manage Users
                </a>
                <a href="{{ route('admin.vocabulary.words.index') }}" class="block w-full px-6 py-3 bg-white/5 border border-purple-900/30 text-white text-center font-semibold rounded-xl hover:bg-white/10 transition-all">
                    Manage Words
                </a>
            </div>

            <div class="mt-8 pt-6 border-t border-purple-900/30 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-red-400 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
