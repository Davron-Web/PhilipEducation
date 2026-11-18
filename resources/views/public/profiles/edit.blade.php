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
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="9" cy="9" r="2" /><path d="m21 15-5-5L5 21" /></svg>
                        Аватар
                    </h2>
                    <p class="mb-4 text-sm text-ink/60">Выберите аватар из набора — сохраняется сразу по клику, без отдельной кнопки.</p>

                    <div class="grid grid-cols-4 gap-3 sm:grid-cols-6 lg:grid-cols-8">
                        @foreach (config('avatars.files', []) as $filename)
                            <form method="POST" action="{{ route('profiles.avatar.update') }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="avatar" value="{{ $filename }}">
                                <button
                                    type="submit"
                                    aria-label="Выбрать этот аватар"
                                    class="block aspect-square w-full overflow-hidden rounded-full border-[3px] transition hover:scale-105 hover:border-brand/60 {{ $user->avatar === $filename ? 'border-brand' : 'border-transparent' }}"
                                >
                                    <img src="{{ asset('avatars/'.$filename) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                </button>
                            </form>
                        @endforeach
                    </div>

                    @error('avatar')
                        <p class="mt-3 text-xs font-semibold text-red-500">{{ $message }}</p>
                    @enderror
                </x-ui.card>

                <x-ui.card :hover="false" class="mt-6">
                    <h2 class="mb-4 flex items-center gap-2 text-base font-bold text-ink">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0" /><circle cx="12" cy="7" r="4" /></svg>
                        Данные профиля
                    </h2>

                    <form method="POST" action="{{ route('profiles.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <x-ui.input label="Полное имя" name="name" value="{{ old('name', $user->name) }}" required />

                        <div>
                            <x-ui.input label="Имя латиницей (для сертификата)" name="name_latin" value="{{ old('name_latin', $user->name_latin) }}" placeholder="Ivan Petrov" />
                            <p class="mt-1.5 text-xs text-ink/50">Если оставить пустым, сертификат покажет обычное имя выше — латиница просто выглядит уместнее на документе на английском.</p>
                            @error('name_latin')
                                <p class="mt-1.5 text-xs font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-ui.input label="Email" type="email" name="email" value="{{ old('email', $user->email) }}" required />

                        <label class="flex items-center gap-2 text-sm text-ink/70">
                            <input
                                type="checkbox"
                                name="show_in_leaderboard"
                                value="1"
                                class="accent-brand"
                                @checked(old('show_in_leaderboard', $user->show_in_leaderboard))
                            >
                            Показывать меня в рейтинге «Лидеры»
                        </label>
                        <p class="-mt-2 text-xs text-ink/50">Если выключить, вы пропадёте из общего списка, но своё место всё равно сможете увидеть.</p>

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

                {{-- Опасная зона: удаление аккаунта требует пароля, потому что
                     операция необратима и запускается из открытой вкладки. --}}
                <x-ui.card :hover="false" class="mt-6 border-red-500/30" x-data="{ confirming: {{ $errors->userDeletion->any() ? 'true' : 'false' }} }">
                    <h2 class="mb-1 flex items-center gap-2 text-base font-bold text-red-500">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
                        Удаление аккаунта
                    </h2>
                    <p class="mb-4 text-sm text-ink/60">
                        Вместе с аккаунтом безвозвратно удаляются прогресс, результаты тестов,
                        достижения и сертификаты. Восстановить их будет нельзя.
                    </p>

                    <button
                        type="button"
                        x-show="!confirming"
                        @click="confirming = true"
                        class="inline-flex items-center gap-2 rounded border-2 border-red-500/40 px-4 py-2 text-xs font-bold uppercase tracking-wider text-red-500 transition hover:bg-red-500/10"
                    >Удалить аккаунт</button>

                    <form method="POST" action="{{ route('profiles.destroy') }}" class="space-y-4" x-show="confirming" style="display:none">
                        @csrf
                        @method('DELETE')

                        <x-ui.input
                            label="Введите пароль для подтверждения"
                            type="password"
                            name="password"
                            :error="$errors->userDeletion->first('password')"
                            required
                        />

                        <div class="flex flex-wrap gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded bg-red-500 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white transition hover:bg-red-600"
                            >Удалить навсегда</button>
                            <button type="button" @click="confirming = false" class="text-xs font-bold uppercase tracking-wider text-ink/50 hover:text-ink">Отмена</button>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            <div>
                <x-ui.card :hover="false" class="text-center">
                    <span class="mx-auto mb-3 flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand to-sky text-xl font-extrabold text-white">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="Аватар" class="h-full w-full object-cover">
                        @else
                            {{ $user->initial() }}
                        @endif
                    </span>
                    <h3 class="font-bold text-ink">{{ $user->name }}</h3>
                    <p class="text-sm text-ink/50">{{ $user->email }}</p>
                </x-ui.card>
            </div>
        </div>
    </div>
@endsection
