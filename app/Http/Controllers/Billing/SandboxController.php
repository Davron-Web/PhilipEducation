<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Payment;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\Request;

/**
 * Имитация страницы оплаты банка — только для разработки.
 * Показывает две кнопки вместо формы карты и сразу проводит платёж
 * через тот же SubscriptionService, что и настоящий вебхук.
 *
 * На боевом сервере маршруты этого контроллера не регистрируются
 * (см. routes/web.php).
 */
class SandboxController extends Controller
{
    public function show(Request $request, string $payment)
    {
        $payment = Payment::where('reference', $payment)->firstOrFail();

        return view('billing.sandbox', compact('payment'));
    }

    public function pay(Request $request, string $payment, SubscriptionService $subscriptions)
    {
        $payment = Payment::where('reference', $payment)->firstOrFail();

        if ($request->boolean('success')) {
            $subscriptions->markPaid($payment, 'sandbox-'.$payment->reference, ['sandbox' => true]);
        } else {
            $subscriptions->markFailed($payment, ['sandbox' => true, 'cancelled' => true]);
        }

        return redirect()->route('billing.return', ['reference' => $payment->reference]);
    }
}
