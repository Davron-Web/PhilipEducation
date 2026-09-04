<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Только чтение: платёж — след операции провайдера, править его руками нельзя. */
class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with(['user', 'plan'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('search'), fn ($q, $search) => $q
                ->where('reference', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $totals = [
            'paid' => (int) Payment::where('status', Payment::STATUS_PAID)->sum('amount_minor'),
            'pending' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'failed' => Payment::where('status', Payment::STATUS_FAILED)->count(),
        ];

        return view('admin.billing.payments.index', compact('payments', 'totals'));
    }

    public function show(Payment $payment): View
    {
        return view('admin.billing.payments.show', [
            'payment' => $payment->load(['user', 'plan', 'subscription', 'invoice']),
        ]);
    }
}
