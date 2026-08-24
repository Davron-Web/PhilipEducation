@extends('layouts.app')

@section('title', 'Подтверждение почты')
@section('page_title', 'Подтвердите email')

@section('content')
    <x-auth.panel title="Подтвердите почту" subtitle="Мы отправили ссылку для подтверждения на ваш email">
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                Новая ссылка отправлена на указанный при регистрации email.
            </div>
        @endif

        <p class="mb-5 text-sm leading-relaxed text-ink/70">
            Спасибо за регистрацию! Перед началом работы подтвердите email, перейдя по ссылке из письма.
            Если письмо не пришло — отправим ещё раз.
        </p>

        <div class="flex items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-ui.button type="submit" variant="primary">Отправить письмо ещё раз</x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-ink/60 underline-offset-2 hover:text-brand hover:underline">Выйти</button>
            </form>
        </div>
    </x-auth.panel>
@endsection
