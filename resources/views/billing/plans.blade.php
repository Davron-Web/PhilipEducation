{{-- Страница тарифов: бесплатный уровень + три платных плана. --}}
@extends('layouts.app')

@section('title', __('site.billing.title'))
@section('page_title', __('site.billing.title'))

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ __('site.billing.nav') }}</p>
            <h1 class="mt-3 font-display text-[32px] font-semibold text-ink sm:text-[38px]">{{ __('site.billing.title') }}</h1>
            <div class="mx-auto mt-4 h-[3px] w-14 bg-gradient-to-r from-[#C9A961] to-[#A78BFA]"></div>
            <p class="mt-4 text-[16px] text-ink/60">{{ __('site.billing.subtitle') }}</p>
        </div>

        @if ($current)
            <div class="mx-auto mt-10 max-w-2xl rounded-2xl border border-brand/30 bg-brand/5 p-5 text-center" data-reveal>
                <p class="font-bold text-ink">
                    {{ __('site.billing.current') }}: {{ $current->plan->name }}
                </p>
                <p class="mt-1 text-sm text-ink/60">
                    {{ __('site.billing.active_until', ['date' => $current->ends_at->format('d.m.Y')]) }}
                    — {{ __('site.billing.days_left', ['count' => $current->daysLeft()]) }}
                </p>

                @if ($current->status !== \App\Models\Billing\Subscription::STATUS_CANCELLED)
                    <form method="POST" action="{{ route('billing.cancel') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="text-xs font-bold uppercase tracking-wider text-ink/50 underline hover:text-brand">
                            {{ __('site.billing.cancel') }}
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <div class="mt-12 grid gap-6 lg:grid-cols-4">
            {{-- Бесплатный уровень --}}
            <div class="rounded-2xl border border-line bg-armor2 p-6 shadow-soft" data-reveal>
                <p class="font-display text-lg font-semibold text-ink">{{ __('site.billing.free_title') }}</p>
                <p class="mt-3 font-display text-3xl font-semibold text-ink">0</p>
                <p class="text-sm text-ink/50">TJS</p>
                <p class="mt-4 text-[14px] leading-relaxed text-ink/60">{{ __('site.billing.free_text') }}</p>
            </div>

            @foreach ($plans as $plan)
                @php $isCurrent = $current && $current->plan_id === $plan->id; @endphp
                <div
                    class="card-lift relative overflow-hidden rounded-2xl border p-6 shadow-soft {{ $plan->code === 'yearly' ? 'border-brand/40 bg-brand/[.03]' : 'border-line bg-armor2' }}"
                    data-reveal
                >
                    @if ($plan->code === 'yearly')
                        <span class="absolute right-5 top-5 rounded bg-gold px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-navy">
                            {{ __('site.home.levels_popular') }}
                        </span>
                    @endif

                    <p class="font-display text-lg font-semibold text-ink">{{ $plan->name }}</p>
                    <p class="mt-3 font-display text-3xl font-semibold text-ink">{{ number_format($plan->price, 2, '.', ' ') }}</p>
                    <p class="text-sm text-ink/50">{{ $plan->currency }}</p>
                    <p class="mt-2 text-[13px] text-ink/45">
                        {{ __('site.billing.per_day', ['price' => number_format($plan->price_per_day, 2, '.', ' ')]) }}
                    </p>

                    @auth
                        <form method="POST" action="{{ route('billing.checkout', $plan) }}" class="mt-6">
                            @csrf
                            <x-ui.button type="submit" class="w-full" :variant="$plan->code === 'yearly' ? 'primary' : 'outline'">
                                {{ $isCurrent ? __('site.billing.current') : __('site.billing.choose') }}
                            </x-ui.button>
                        </form>
                    @else
                        <x-ui.button :href="route('login')" class="mt-6 w-full" :variant="$plan->code === 'yearly' ? 'primary' : 'outline'">
                            {{ __('site.billing.subscribe') }}
                        </x-ui.button>
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
@endsection
