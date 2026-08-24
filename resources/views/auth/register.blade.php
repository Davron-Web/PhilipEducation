@extends('layouts.app')

@section('title', 'Регистрация')
@section('page_title', 'Создать аккаунт')
@section('meta_description', 'Зарегистрируйтесь в Philip Education бесплатно и начните изучать английский язык уже сегодня.')

@section('content')
    <x-auth.panel title="Начать бесплатно" subtitle="Создайте аккаунт и начните учить английский">
        <x-slot:footer>
            Уже есть аккаунт?
            <a href="{{ route('login') }}" class="font-bold text-brand hover:underline">Войти</a>
        </x-slot:footer>

        @if ($errors->any() && !$errors->has(['name', 'email', 'password', 'password_confirmation']))
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <x-ui.input label="Имя" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ваше имя" />
            <x-ui.input label="Email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-ui.input label="Пароль" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-ui.input label="Подтверждение пароля" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />

            <label class="flex items-start gap-2 text-sm text-ink/70">
                <input type="checkbox" name="terms" required class="mt-0.5 rounded border-ink/30 text-brand focus:ring-brand/30">
                Я согласен с условиями использования
            </label>

            <x-ui.button type="submit" variant="primary" class="w-full">Зарегистрироваться</x-ui.button>
        </form>
    </x-auth.panel>
@endsection
