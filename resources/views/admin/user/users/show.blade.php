@extends('layouts.admin')

@section('title', 'Пользователь: ' . $user->name)

@section('content')
    <x-admin.page-header :title="$user->name" :backRoute="route('admin.user.users.index')">
        <x-slot:actions>
            <a href="{{ route('admin.user.users.edit', $user) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $user->id }}</span></div>
        <div class="info-row"><span class="info-label">Имя</span><span class="info-value" style="font-weight:700">{{ $user->name }}</span></div>
        <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $user->email }}</span></div>
        <div class="info-row"><span class="info-label">Роль</span><span class="info-value">{{ $user->role?->name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Уровень</span><span class="info-value">{{ $user->level?->code ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Баллы</span><span class="info-value"><span class="badge badge-primary">{{ $user->points }}</span></span></div>
        <div class="info-row">
            <span class="info-label">Активен</span>
            <span class="info-value">
                @if($user->is_active)
                    <span class="badge badge-success">Да</span>
                @else
                    <span class="badge badge-secondary">Нет</span>
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Создан</span><span class="info-value">{{ $user->created_at }}</span></div>
        <div class="info-row"><span class="info-label">Обновлён</span><span class="info-value">{{ $user->updated_at }}</span></div>
    </div>

    <div class="stats-grid">
        <x-admin.stat-card label="Уроков пройдено" :value="$user->progress->where('is_completed', true)->count()" color="blue" />
        <x-admin.stat-card label="Тестов сдано" :value="$user->results->where('passed', true)->count()" color="green" />
        <x-admin.stat-card label="Достижений" :value="$user->achievements->count()" color="yellow" />
    </div>

    {{-- Премиум-доступ: выдача без оплаты --}}
    @php $subscription = $user->activeSubscription(); @endphp

    <div class="card">
        <h3>Премиум-доступ</h3>

        @if ($subscription)
            <div class="info-row">
                <span class="info-label">Статус</span>
                <span class="info-value">
                    <span class="badge badge-success">Активен</span>
                    @if ($subscription->isGift())
                        <span class="badge badge-warning">Подарок</span>
                    @else
                        <span class="badge badge-primary">Оплачен</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Действует до</span>
                <span class="info-value">
                    {{ $subscription->isLifetime() ? 'бессрочно' : $subscription->ends_at->format('d.m.Y').' ('.$subscription->daysLeft().' дн.)' }}
                </span>
            </div>
            @if ($subscription->note)
                <div class="info-row"><span class="info-label">Примечание</span><span class="info-value">{{ $subscription->note }}</span></div>
            @endif
            @if ($subscription->granted_by)
                <div class="info-row">
                    <span class="info-label">Выдал</span>
                    <span class="info-value">{{ \App\Models\User::find($subscription->granted_by)?->name ?? '—' }}</span>
                </div>
            @endif
        @else
            <div class="info-row">
                <span class="info-label">Статус</span>
                <span class="info-value"><span class="badge badge-secondary">Нет подписки</span></span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.user.users.premium.grant', $user) }}" style="margin-top:16px;display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">
            @csrf
            <label style="display:flex;flex-direction:column;gap:4px">
                <span style="font-size:12px;font-weight:600">Срок</span>
                <select name="duration" class="form-input" style="min-width:170px">
                    <option value="7">7 дней</option>
                    <option value="30" selected>30 дней</option>
                    <option value="90">90 дней</option>
                    <option value="365">1 год</option>
                    <option value="lifetime">Бессрочно</option>
                </select>
            </label>
            <label style="display:flex;flex-direction:column;gap:4px;flex:1;min-width:220px">
                <span style="font-size:12px;font-weight:600">Примечание (необязательно)</span>
                <input type="text" name="note" class="form-input" placeholder="например: победитель конкурса" maxlength="255">
            </label>
            <button type="submit" class="btn btn-primary">
                {{ $subscription ? 'Продлить премиум' : 'Подарить премиум' }}
            </button>
        </form>

        @if ($subscription && $subscription->isGift())
            <form method="POST" action="{{ route('admin.user.users.premium.revoke', $user) }}" style="margin-top:10px"
                  onsubmit="return confirm('Отозвать премиум у {{ $user->name }}? Доступ закроется сразу.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary">Отозвать премиум</button>
            </form>
        @elseif ($subscription)
            <p style="margin-top:10px;font-size:13px;opacity:.7">
                Подписка оплачена — отозвать её здесь нельзя, возврат денег оформляется через банк.
            </p>
        @endif
    </div>
@endsection
