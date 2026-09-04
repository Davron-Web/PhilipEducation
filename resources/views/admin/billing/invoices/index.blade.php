@extends('layouts.admin')

@section('title', 'Счета')

@section('content')
    <x-admin.page-header title="Счета" />

    <section class="stats-grid">
        <x-admin.stat-card color="yellow" label="Выставлено на сумму" :value="number_format($total / 100, 0, ',', ' ')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' />
    </section>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Номер счёта или email..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.billing.invoices.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:180px">Номер</th>
                    <th>Ученик</th>
                    <th>Тариф</th>
                    <th style="width:140px">Сумма</th>
                    <th style="width:140px">Выставлен</th>
                    <th style="width:120px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="title-cell">{{ $invoice->number }}</td>
                        <td class="text-cell">{{ $invoice->user?->email ?? '—' }}</td>
                        <td>{{ $invoice->plan_name }}</td>
                        <td><strong>{{ number_format($invoice->amount_minor / 100, 2, ',', ' ') }} {{ $invoice->currency }}</strong></td>
                        <td>{{ $invoice->issued_at->format('d.m.Y') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.billing.invoices.show', $invoice) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="6">Счетов пока нет</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($invoices->hasPages())<div class="pagination">{{ $invoices->withQueryString()->links() }}</div>@endif
@endsection
