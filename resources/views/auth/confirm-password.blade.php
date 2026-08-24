@extends('layouts.app')

@section('title', 'Подтверждение пароля')
@section('page_title', 'Подтвердите пароль')

@section('content')
    <x-auth.panel title="Подтвердите пароль" subtitle="Это защищённый раздел — подтвердите пароль перед продолжением">
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <x-ui.input label="Пароль" type="password" name="password" required autofocus autocomplete="current-password" placeholder="••••••••" />

            <x-ui.button type="submit" variant="primary" class="w-full">Подтвердить</x-ui.button>
        </form>
    </x-auth.panel>
@endsection
