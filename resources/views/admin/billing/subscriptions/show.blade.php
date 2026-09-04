@extends('layouts.admin')

@section('title', 'Подписка #'.$subscription->id)

@section('content')
    <x-admin.page-header :title="'Подписка #'.$subscription->id" :backRoute="route('admin.billing.subscriptions.index')" />

    <div class="card">
        <p>Ученик: <strong>{{ $subscription->user?->name }}</strong> ({{ $subscription->user?->email }})</p>
        <p>Тариф: <strong>{{ $subscription->plan?->name ?? 'Подарок администратора' }}</strong></p>
        <p>Период: {{ optional($subscription->starts_at)->format('d.m.Y') ?: '—' }} — {{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : 'бессрочно' }}</p>
        <p>Статус: <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : 'warning' }}">{{ $subscription->status }}</span></p>
        @if ($subscription->note)
            <p>Примечание: {{ $subscription->note }}</p>
        @endif
    </div>

    <div class="card table-card">
        <h3>Платежи по подписке</h3>
        <div class="table-scroll">
            <table class="tbl">
                <thead><tr><th>Ссылка</th><th>Сумма</th><th>Статус</th><th>Оплачен</th></tr></thead>
                <tbody>
                @forelse($subscription->payments as $payment)
                    <tr>
                        <td class="text-cell">{{ $payment->reference }}</td>
                        <td><strong>{{ number_format($payment->amount_minor / 100, 2, ',', ' ') }} {{ $payment->currency }}</strong></td>
                        <td><span class="badge badge-{{ $payment->status === 'paid' ? 'success' : 'warning' }}">{{ $payment->status }}</span></td>
                        <td>{{ optional($payment->paid_at)->format('d.m.Y H:i') ?: '—' }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">Платежей нет — подписка выдана администратором</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
