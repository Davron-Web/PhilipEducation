{{-- Подтверждение почты кодом из письма. Ссылка из того же письма ведёт
     сюда же по результату — оба способа отмечают адрес подтверждённым. --}}
@extends('layouts.app')

@section('title', 'Подтверждение почты')
@section('page_title', 'Подтвердите email')

@section('content')
    <x-auth.panel title="Подтвердите почту" subtitle="Введите код из письма">

        @if (session('status') === 'verification-link-sent')
            <div class="mb-4 rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm font-semibold text-green-700 dark:text-green-400">
                Новый код отправлен. Проверьте почту.
            </div>
        @endif

        {{-- Адрес показываем крупно: человек с опечаткой при регистрации
             должен увидеть её здесь, а не гадать, почему письмо не пришло. --}}
        <div class="mb-5 rounded-xl border border-line bg-surface2 px-4 py-3">
            <p class="text-sm text-ink/60">Код отправлен на</p>
            <p class="mt-0.5 break-all font-semibold text-ink">{{ auth()->user()->email }}</p>
            <a href="{{ route('profiles.edit') }}" class="mt-1 inline-block text-sm font-semibold text-brand hover:underline">
                Изменить адрес
            </a>
        </div>

        <form method="POST" action="{{ route('verification.code') }}" class="space-y-4">
            @csrf

            <div>
                <label for="code" class="mb-1.5 block text-sm font-bold text-ink">Код из письма</label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    {{-- inputmode="numeric" открывает цифровую клавиатуру на
                         телефоне; type="text" вместо number — тот режет
                         ведущие нули и рисует ненужные стрелки. --}}
                    inputmode="numeric"
                    pattern="[0-9]*"
                    autocomplete="one-time-code"
                    maxlength="6"
                    required
                    autofocus
                    placeholder="000000"
                    value="{{ old('code') }}"
                    class="w-full rounded-xl border-2 bg-armor2 px-4 py-3 text-center text-2xl font-bold tracking-[0.4em] text-ink placeholder:text-ink/20 focus:outline-none focus:ring-4 focus:ring-brand/15 @error('code') border-red-400 @else border-line focus:border-brand @enderror"
                >
                @error('code')
                    <p class="mt-1.5 text-sm font-semibold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <x-ui.button type="submit" class="w-full justify-center">Подтвердить</x-ui.button>
        </form>

        <p class="mt-5 text-sm leading-relaxed text-ink/60">
            Код действует {{ App\Services\EmailVerificationCodeService::LIFETIME_MINUTES }} минут.
            В том же письме есть кнопка — она подтверждает адрес без ввода кода.
        </p>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-ui.button type="submit" variant="outline">Отправить новый код</x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-ink/60 underline-offset-2 hover:text-brand hover:underline">Выйти</button>
            </form>
        </div>
    </x-auth.panel>
@endsection

@push('scripts')
    <script>
        // Оставляем в поле только цифры: при вставке из письма туда часто
        // попадают пробелы и невидимые символы форматирования.
        document.getElementById('code')?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    </script>
@endpush
