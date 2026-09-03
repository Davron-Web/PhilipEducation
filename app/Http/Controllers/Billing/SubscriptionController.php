<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Payment;
use App\Models\Billing\Plan;
use App\Services\Payment\PaymentGateway;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptions,
        private readonly PaymentGateway $gateway,
    ) {}

    /** Страница тарифов. */
    public function plans()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $current = Auth::check() ? Auth::user()->activeSubscription() : null;

        return view('billing.plans', compact('plans', 'current'));
    }

    /** Создаёт платёж и отправляет пользователя на страницу оплаты провайдера. */
    public function checkout(Request $request, Plan $plan)
    {
        abort_unless($plan->is_active, 404);

        $result = $this->subscriptions->startCheckout($request->user(), $plan);

        return redirect()->away($result['checkout_url']);
    }

    /**
     * Куда провайдер возвращает пользователя после оплаты. Доступ здесь
     * НЕ выдаётся: этой странице нельзя доверять, пользователь может
     * открыть её сам. Подписку открывает только вебхук.
     */
    public function return(Request $request)
    {
        $payment = Payment::where('reference', $request->query('reference'))->first();

        if ($payment && $payment->status === Payment::STATUS_PAID) {
            return redirect()->route('profiles.index')->with('success', __('site.billing.paid'));
        }

        return redirect()->route('billing.plans')->with('info', __('site.billing.pending'));
    }

    /**
     * Вебхук провайдера — единственное место, где платёж признаётся
     * состоявшимся. Маршрут вынесен из-под CSRF и auth: запрос приходит
     * от банка, а не от браузера пользователя.
     */
    public function webhook(Request $request)
    {
        $result = $this->gateway->parseWebhook($request);

        if (! $result) {
            Log::warning('Платёжный вебхук не распознан', ['provider' => $this->gateway->name()]);

            return response()->json(['ok' => false], 400);
        }

        $payment = Payment::where('reference', $result->reference)->first();

        if (! $payment) {
            Log::warning('Вебхук для неизвестного платежа', ['reference' => $result->reference]);

            return response()->json(['ok' => false], 404);
        }

        if ($result->paid) {
            $this->subscriptions->markPaid($payment, $result->providerPaymentId, $result->payload);
        } else {
            $this->subscriptions->markFailed($payment, $result->payload);
        }

        return response()->json(['ok' => true]);
    }

    /** Отказ от продления: доступ остаётся до конца оплаченного периода. */
    public function cancel(Request $request)
    {
        $subscription = $request->user()->activeSubscription();

        if ($subscription) {
            $this->subscriptions->cancel($subscription);
        }

        return back()->with('success', __('site.billing.cancelled'));
    }
}
