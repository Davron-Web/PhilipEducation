@extends('layouts.admin')

@section('title', 'Платёж #'.$payment->id)

@section('content')
    <x-admin.page-header :title="'Платёж #'.$payment->id" :backRoute="route('admin.billing.payments.index')" />

    <div class="card">
        <p>Ученик: <strong>{{ $payment->user?->name }}</strong> ({{ $payment->user?->email }})</p>
        <p>Тариф: <strong>{{ $payment->plan?->name ?? '—' }}</strong></p>
        <p>Сумма: <strong>{{ number_format($payment->amount_minor / 100, 2, ',', ' ') }} {{ $payment->currency }}</strong></p>
        <p>Провайдер: {{ $payment->provider }} · ссылка <code>{{ $payment->reference }}</code></p>
        <p>Идентификатор у провайдера: <code>{{ $payment->provider_payment_id ?: '—' }}</code></p>
        <p>Статус: <span class="badge badge-{{ $payment->status === 'paid' ? 'success' : 'warning' }}">{{ $payment->status }}</span></p>
        <p>Оплачен: {{ optional($payment->paid_at)->format('d.m.Y H:i') ?: '—' }}</p>
        @if ($payment->invoice)
            <p>Счёт: <a href="{{ route('admin.billing.invoices.show', $payment->invoice) }}">{{ $payment->invoice->number }}</a></p>
        @endif
    </div>

    @if ($payment->payload)
        <div class="card">
            <h3>Ответ провайдера</h3>
            <pre style="overflow:auto;font-size:12px">{{ json_encode($payment->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
@endsection
