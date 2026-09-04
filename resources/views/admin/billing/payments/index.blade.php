@extends('layouts.admin')

@section('title', 'Платежи')

@section('content')
    <x-admin.page-header title="Платежи" />

    <section class="stats-grid">
        <x-admin.stat-card color="yellow" label="Получено всего" :value="number_format($totals['paid'] / 100, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' />
        <x-admin.stat-card color="blue" label="Ожидают оплаты" :value="$totals['pending']"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' />
        <x-admin.stat-card color="blue" label="Неудачных" :value="$totals['failed']"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Ссылка платежа или email..." value="{{ request('search') }}">
            <select name="status" class="select">
                <option value="">Все статусы</option>
                @foreach (['paid' => 'Оплачен', 'pending' => 'Ожидает', 'failed' => 'Неудача', 'cancelled' => 'Отменён'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.billing.payments.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Ученик</th>
                    <th>Тариф</th>
                    <th style="width:130px">Сумма</th>
                    <th style="width:110px">Провайдер</th>
                    <th style="width:110px">Статус</th>
                    <th style="width:150px">Оплачен</th>
                    <th style="width:120px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="id-cell">#{{ $payment->id }}</td>
                        <td class="title-cell">
                            {{ $payment->user?->name ?? '—' }}
                            <div class="text-cell">{{ $payment->user?->email }}</div>
                        </td>
                        <td>{{ $payment->plan?->name ?? '—' }}</td>
                        <td><strong>{{ number_format($payment->amount_minor / 100, 2, ',', ' ') }} {{ $payment->currency }}</strong></td>
                        <td class="text-cell">{{ $payment->provider }}</td>
                        <td>
                            <span class="badge badge-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td>{{ optional($payment->paid_at)->format('d.m.Y H:i') ?: '—' }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.billing.payments.show', $payment) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Платежей не найдено</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($payments->hasPages())<div class="pagination">{{ $payments->withQueryString()->links() }}</div>@endif
@endsection
