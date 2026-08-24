@extends('layouts.app')

@section('title', 'Восстановление пароля')
@section('page_title', 'Забыли пароль?')

@section('content')
    <x-auth.panel title="Восстановление пароля" subtitle="Укажите email — пришлём ссылку для сброса пароля">
        <x-slot:footer>
            Вспомнили пароль?
            <a href="{{ route('login') }}" class="font-bold text-brand hover:underline">Войти</a>
        </x-slot:footer>

        @if (session('status'))
            <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <x-ui.input label="Email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />

            <x-ui.button type="submit" variant="primary" class="w-full">Отправить ссылку</x-ui.button>
        </form>
    </x-auth.panel>
@endsection
