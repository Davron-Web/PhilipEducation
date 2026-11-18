{{-- Тарифы на дашборде — витрина ценности, не отдельный биллинг: PRO и
     PREMIUM ведут на ту же /plans, где уже есть реальный чекаут (в проекте
     один платный уровень доступа, см. config/plans.php). Названия/буллеты —
     из config/plans.php, цена и валюта — реальные из App\Models\Billing\Plan
     (DashboardController::pricingPlans()). Карточки — те же x-ui.card, что
     и весь остальной дашборд, PRO выделен только рамкой/свечением через
     .plan-card-highlight (см. <style> в user/dashboard.blade.php). --}}
@props(['plans' => []])

<section class="mt-2">
    <div class="mb-4 flex items-center gap-2">
        <span class="acc-icon flex h-9 w-9 items-center justify-center rounded-xl" style="--accent: var(--pe-sky)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2" /><path d="M2 10h20" /></svg>
        </span>
        <h2 class="text-lg font-bold text-ink">Тарифы</h2>
    </div>

    <div class="grid items-stretch gap-6 md:grid-cols-3">
        @foreach ($plans as $plan)
            <x-ui.card
                :hover="true"
                class="!p-6 flex h-full flex-col {{ $plan['highlight'] ? 'plan-card-highlight' : '' }} {{ $plan['highlight'] ? 'order-first md:order-none' : '' }}"
            >
                @if (! empty($plan['badge']))
                    <span class="absolute right-6 top-6 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white" style="background: var(--pe-sky)">
                        {{ $plan['badge'] }}
                    </span>
                @endif

                <h3 class="text-lg font-bold text-ink">{{ $plan['name'] }}</h3>
                <p class="mt-1 text-sm text-ink/60">{{ $plan['tagline'] }}</p>

                <p class="mt-5 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-ink">{{ $plan['price_label'] }}</span>
                    <span class="text-sm text-ink/50">/ {{ $plan['period_label'] }}</span>
                </p>

                <ul class="mt-5 space-y-2.5 text-sm text-ink/70">
                    @foreach ($plan['features'] as $feature)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @if ($plan['is_current'])
                    <x-ui.button type="button" disabled variant="outline" class="mt-auto w-full">Текущий план</x-ui.button>
                @else
                    <x-ui.button :href="route('billing.plans')" :variant="$plan['highlight'] ? 'primary' : 'outline'" class="mt-auto w-full">
                        {{ $plan['cta'] }}
                    </x-ui.button>
                @endif
            </x-ui.card>
        @endforeach
    </div>
</section>
