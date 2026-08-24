@extends('layouts.app')

@section('title', 'Новый пароль')
@section('page_title', 'Придумайте новый пароль')

@section('content')
    <x-auth.panel title="Новый пароль" subtitle="Придумайте новый пароль для входа">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-ui.input label="Email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-ui.input label="Новый пароль" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-ui.input label="Подтверждение пароля" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />

            <x-ui.button type="submit" variant="primary" class="w-full">Сохранить пароль</x-ui.button>
        </form>
    </x-auth.panel>
@endsection
