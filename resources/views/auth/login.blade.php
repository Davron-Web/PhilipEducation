@extends('layouts.app')

@section('title', 'Вход')
@section('page_title', 'Вход в аккаунт')
@section('meta_description', 'Войдите в Philip Education и продолжите изучение английского языка.')

@section('content')
    <x-auth.panel title="С возвращением!" subtitle="Войдите, чтобы продолжить обучение">
        <x-slot:footer>
            Ещё нет аккаунта?
            <a href="{{ route('register') }}" class="font-bold text-brand hover:underline">Зарегистрироваться</a>
        </x-slot:footer>

        @if (session('error'))
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</div>
        @endif
        @if (session('status'))
            <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <x-ui.input label="Email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-ui.input label="Пароль" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-ink/70">
                    <input type="checkbox" name="remember" class="rounded border-ink/30 text-brand focus:ring-brand/30">
                    Запомнить меня
                </label>
                <a href="{{ route('password.request') }}" class="font-semibold text-brand hover:underline">Забыли пароль?</a>
            </div>

            <x-ui.button type="submit" variant="primary" class="w-full">Войти</x-ui.button>
        </form>
    </x-auth.panel>
@endsection
