{{-- Имитация страницы оплаты банка. Только для разработки: маршруты
     этой страницы не регистрируются в production (см. routes/web.php).

     Формы ввода карты здесь намеренно нет — на реальном провайдере её
     показывает сам банк на своей стороне, и данные карты никогда не
     проходят через наш сервер. --}}
@extends('layouts.app')

@section('title', __('site.billing.sandbox_notice'))
@section('page_title', __('site.billing.title'))

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-lg items-center px-4 py-16">
        <div class="w-full">
            <div class="mb-6 rounded-2xl border border-sun/30 bg-sun/10 px-4 py-3 text-center text-sm font-semibold text-sun">
                {{ __('site.billing.sandbox_notice') }}
            </div>

            <x-ui.card :hover="false">
                <p class="text-xs font-bold uppercase tracking-wider text-ink/40">{{ $payment->plan->name }}</p>
                <p class="mt-2 font-display text-3xl font-semibold text-ink">
                    {{ number_format($payment->amount, 2, '.', ' ') }} {{ $payment->currency }}
                </p>
                <p class="mt-1 font-mono text-xs text-ink/40">{{ $payment->reference }}</p>

                <div class="mt-6 space-y-3">
                    <form method="POST" action="{{ route('billing.sandbox.pay', $payment->reference) }}">
                        @csrf
                        <input type="hidden" name="success" value="1">
                        <x-ui.button type="submit" class="w-full">{{ __('site.billing.sandbox_pay') }}</x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('billing.sandbox.pay', $payment->reference) }}">
                        @csrf
                        <input type="hidden" name="success" value="0">
                        <x-ui.button type="submit" variant="outline" class="w-full">{{ __('site.billing.sandbox_fail') }}</x-ui.button>
                    </form>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection
