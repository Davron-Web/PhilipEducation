<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Subscription;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Просмотр подписок. Создаются они оплатой или подарком от админа
 * (UserSubscriptionController), поэтому здесь только список, карточка
 * и отмена — руками подписку не заводят.
 */
class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('search'), fn ($q, $search) => $q->whereHas(
                'user',
                fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.billing.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription): View
    {
        return view('admin.billing.subscriptions.show', [
            'subscription' => $subscription->load(['user', 'plan', 'payments']),
        ]);
    }

    public function cancel(Subscription $subscription, SubscriptionService $subscriptions): RedirectResponse
    {
        $subscriptions->cancel($subscription);

        return redirect()
            ->route('admin.billing.subscriptions.index')
            ->with('success', 'Подписка отменена — доступ сохранится до конца оплаченного срока');
    }
}
