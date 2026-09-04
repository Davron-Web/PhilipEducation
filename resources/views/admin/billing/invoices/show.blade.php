@extends('layouts.admin')

@section('title', $invoice->number)

@section('content')
    <x-admin.page-header :title="$invoice->number" :backRoute="route('admin.billing.invoices.index')" />

    <div class="card">
        <p>Ученик: <strong>{{ $invoice->user?->name }}</strong> ({{ $invoice->user?->email }})</p>
        <p>Тариф: <strong>{{ $invoice->plan_name }}</strong></p>
        <p>Сумма: <strong>{{ number_format($invoice->amount_minor / 100, 2, ',', ' ') }} {{ $invoice->currency }}</strong></p>
        <p>Выставлен: {{ $invoice->issued_at->format('d.m.Y H:i') }}</p>
        <p>Статус: <span class="badge badge-success">{{ $invoice->status }}</span></p>
        @if ($invoice->payment)
            <p>Платёж: <a href="{{ route('admin.billing.payments.show', $invoice->payment) }}">#{{ $invoice->payment->id }}</a></p>
        @endif
        @if ($invoice->subscription)
            <p>Подписка: <a href="{{ route('admin.billing.subscriptions.show', $invoice->subscription) }}">#{{ $invoice->subscription->id }}</a></p>
        @endif
    </div>
@endsection
