@extends('layouts.admin')

@section('title', 'Тарифы')

@section('content')
    <x-admin.page-header title="Тарифы">
        <x-slot:actions>
            <a href="{{ route('admin.billing.plans.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить тариф
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Название</th>
                    <th style="width:130px">Код</th>
                    <th style="width:110px">Срок</th>
                    <th style="width:130px">Цена</th>
                    <th style="width:110px">Подписок</th>
                    <th style="width:100px">Статус</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td class="id-cell">#{{ $plan->id }}</td>
                        <td class="title-cell">{{ $plan->name }}</td>
                        <td class="text-cell">{{ $plan->code }}</td>
                        <td>{{ $plan->duration_days }} дн.</td>
                        <td><strong>{{ number_format($plan->price_minor / 100, 2, ',', ' ') }} {{ $plan->currency }}</strong></td>
                        <td>{{ $plan->subscriptions_count }}</td>
                        <td>
                            <span class="badge badge-{{ $plan->is_active ? 'success' : 'warning' }}">
                                {{ $plan->is_active ? 'Активен' : 'Скрыт' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.billing.plans.edit', $plan) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.billing.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Удалить тариф?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="8">Тарифов пока нет</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($plans->hasPages())<div class="pagination">{{ $plans->links() }}</div>@endif
@endsection
