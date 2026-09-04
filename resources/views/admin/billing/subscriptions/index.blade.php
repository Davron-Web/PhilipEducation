@extends('layouts.admin')

@section('title', 'Подписки')

@section('content')
    <x-admin.page-header title="Подписки" />

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Имя или email ученика..." value="{{ request('search') }}">
            <select name="status" class="select">
                <option value="">Все статусы</option>
                @foreach (['active' => 'Активна', 'cancelled' => 'Отменена', 'expired' => 'Истекла', 'pending' => 'Ожидает'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.billing.subscriptions.index') }}" class="btn btn-ghost">Сброс</a>
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
                    <th style="width:110px">Источник</th>
                    <th style="width:120px">Начало</th>
                    <th style="width:120px">Окончание</th>
                    <th style="width:110px">Статус</th>
                    <th style="width:200px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td class="id-cell">#{{ $subscription->id }}</td>
                        <td class="title-cell">
                            {{ $subscription->user?->name ?? '—' }}
                            <div class="text-cell">{{ $subscription->user?->email }}</div>
                        </td>
                        <td>{{ $subscription->plan?->name ?? 'Подарок' }}</td>
                        <td>
                            <span class="badge badge-{{ $subscription->source === 'gift' ? 'warning' : 'success' }}">
                                {{ $subscription->source === 'gift' ? 'Подарок' : 'Оплата' }}
                            </span>
                        </td>
                        <td>{{ optional($subscription->starts_at)->format('d.m.Y') ?: '—' }}</td>
                        <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : 'бессрочно' }}</td>
                        <td>
                            <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'danger' : 'warning') }}">
                                {{ $subscription->status }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.billing.subscriptions.show', $subscription) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                @if ($subscription->status === 'active')
                                    <form action="{{ route('admin.billing.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Отменить подписку? Доступ сохранится до конца оплаченного срока.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Отменить</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Подписок не найдено</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($subscriptions->hasPages())<div class="pagination">{{ $subscriptions->withQueryString()->links() }}</div>@endif
@endsection
