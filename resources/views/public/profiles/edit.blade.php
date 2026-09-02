@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('page_description', 'Manage your profile and security')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status') === 'password-updated')
            <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between gap-3 rounded-2xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">
                <span class="flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><path d="M20 6 9 17l-5-5" /></svg>
                    Пароль успешно обновлён.
                </span>
                <button type="button" @click="show = false" class="shrink-0 text-green-600/60 hover:text-green-600 dark:text-green-400/60 dark:hover:text-green-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <div>
                <x-ui.card :hover="false">
                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-ink">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0" /><circle cx="12" cy="7" r="4" /></svg>
                        Данные профиля
                    </h2>

                    <form method="POST" action="{{ route('profiles.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <x-ui.input label="Полное имя" name="name" value="{{ old('name', $user->name) }}" required />
                        <x-ui.input label="Email" type="email" name="email" value="{{ old('email', $user->email) }}" required />

                        <x-ui.button type="submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" /><polyline points="17 21 17 13 7 13 7 21" /><polyline points="7 3 7 8 15 8" /></svg>
                            Сохранить изменения
                        </x-ui.button>
                    </form>
                </x-ui.card>

                <x-ui.card :hover="false" id="password-section" class="mt-6">
                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-ink">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                        Смена пароля
                    </h2>

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <x-ui.input
                            label="Текущий пароль"
                            type="password"
                            name="current_password"
                            :error="$errors->updatePassword->first('current_password')"
                            required
                        />
                        <x-ui.input
                            label="Новый пароль"
                            type="password"
                            name="password"
                            :error="$errors->updatePassword->first('password')"
                            required
                        />
                        <x-ui.input label="Подтвердите новый пароль" type="password" name="password_confirmation" required />

                        <x-ui.button type="submit" variant="outline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                            Обновить пароль
                        </x-ui.button>
                    </form>
                </x-ui.card>
            </div>

            <div>
                <x-ui.card :hover="false" class="text-center">
                    <span class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand to-sky text-xl font-extrabold text-white">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </span>
                    <h3 class="font-bold text-ink">{{ $user->name }}</h3>
                    <p class="text-sm text-ink/50">{{ $user->email }}</p>
                </x-ui.card>
            </div>
        </div>
    </div>
@endsection
