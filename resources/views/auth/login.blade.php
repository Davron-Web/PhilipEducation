@extends('layouts.app')

@section('title', 'Вход')
@section('page_title', 'Вход в аккаунт')
@section('meta_description', 'Войдите в Philip Education и продолжите изучение английского языка.')

@section('content')
    <x-auth.panel title="{{ __('site.auth.login_title') }}" subtitle="{{ __('site.auth.login_subtitle') }}">
        <x-slot:footer>
            {{ __('site.auth.no_account') }}
            <a href="{{ route('register') }}" class="font-bold text-brand hover:underline">{{ __('site.auth.sign_up') }}</a>
        </x-slot:footer>

        @if (session('error'))
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</div>
        @endif
        @if (session('status'))
            <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <x-ui.input label="{{ __('site.auth.email') }}" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-ui.input label="{{ __('site.auth.password') }}" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-ink/70">
                    <input type="checkbox" name="remember" class="rounded border-ink/30 text-brand focus:ring-brand/30">
                    {{ __('site.auth.remember') }}
                </label>
                <a href="{{ route('password.request') }}" class="font-semibold text-brand hover:underline">{{ __('site.auth.forgot') }}</a>
            </div>

            <x-ui.button type="submit" variant="primary" class="w-full">{{ __('site.auth.sign_in') }}</x-ui.button>
        </form>
    </x-auth.panel>
@endsection
