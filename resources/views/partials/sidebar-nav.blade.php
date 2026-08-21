@php
    $navItems = [
        ['route' => 'user.dashboard', 'pattern' => 'user.dashboard', 'icon' => 'bi-house-door', 'label' => 'Главная'],
        ['route' => 'lessons.index', 'pattern' => 'lessons.*', 'icon' => 'bi-journal-bookmark', 'label' => 'Уроки'],
        ['route' => 'grammartopics.index', 'pattern' => 'grammartopics.*', 'icon' => 'bi-diagram-3', 'label' => 'Грамматика'],
        ['route' => 'words.index', 'pattern' => 'words.*', 'icon' => 'bi-translate', 'label' => 'Словарь'],
        ['route' => 'exercises.index', 'pattern' => 'exercises.*', 'icon' => 'bi-pencil-square', 'label' => 'Упражнения'],
        ['route' => 'tests.index', 'pattern' => 'tests.*', 'icon' => 'bi-clipboard-check', 'label' => 'Тесты'],
        ['route' => 'books.index', 'pattern' => 'books.*', 'icon' => 'bi-book', 'label' => 'Книги'],
        ['route' => 'achievements.index', 'pattern' => 'achievements.*', 'icon' => 'bi-trophy', 'label' => 'Достижения'],
        ['route' => 'notifications.index', 'pattern' => 'notifications.*', 'icon' => 'bi-bell', 'label' => 'Уведомления'],
        ['route' => 'profiles.index', 'pattern' => 'profiles.index', 'icon' => 'bi-person-circle', 'label' => 'Профиль'],
        ['route' => 'profiles.edit', 'pattern' => 'profiles.edit', 'icon' => 'bi-gear', 'label' => 'Настройки'],
    ];
@endphp

<ul class="nav nav-pills flex-column sidebar-nav gap-1">
    @foreach($navItems as $item)
        <li class="nav-item">
            <a href="{{ route($item['route']) }}"
               class="nav-link d-flex align-items-center {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }} me-2"></i>
                <span>{{ $item['label'] }}</span>

                @if($item['route'] === 'notifications.index' && ($unreadCount ?? 0) > 0)
                    <span class="badge text-bg-primary rounded-pill ms-auto">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>
    @endforeach
</ul>
