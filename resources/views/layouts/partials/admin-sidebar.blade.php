@php
    $adminNav = [
        ['title' => 'Главная', 'route' => 'admin.dashboard', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
        ['title' => 'Уроки', 'route' => 'admin.content.lessons.index', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
        ['title' => 'Грамматика', 'route' => 'admin.content.grammartopics.index', 'icon' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 3 1-4L16.5 3.5z"/>'],
        ['title' => 'Словарь', 'route' => 'admin.vocabulary.words.index', 'icon' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
        ['title' => 'Выражения', 'route' => 'admin.vocabulary.expressions.index', 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
        ['title' => 'Упражнения', 'route' => 'admin.exercise.exercises.index', 'icon' => '<path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>'],
        ['title' => 'Тесты', 'route' => 'admin.test.tests.index', 'icon' => '<polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>'],
        ['title' => 'Домашние задания', 'route' => 'admin.exercise.exercisequestions.index', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>'],
        ['title' => 'Тестовый вопрос', 'route' => 'admin.test.testquestions.index', 'icon' => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
        ['title' => 'Тестовый ответ', 'route' => 'admin.test.testanswers.index', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/>'],
        ['title' => 'Книги', 'route' => 'admin.book.books.index', 'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M9 7h6M9 11h6"/>'],
        ['title' => 'Уровни', 'route' => 'admin.system.levels.index', 'icon' => '<path d="M2 20h.01"/><path d="M7 20v-4"/><path d="M12 20v-8"/><path d="M17 20V8"/><path d="M22 4v16"/>'],
        ['title' => 'Содержание урока', 'route' => 'admin.content.lessoncontents.index', 'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
        ['title' => 'Достижения', 'route' => 'admin.gamification.achievements.index', 'icon' => '<circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>'],
        ['title' => 'Титулы', 'route' => 'admin.gamification.titles.index', 'icon' => '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>'],
        ['title' => 'Статистика обучения', 'route' => 'admin.gamification.studystatistics.index', 'icon' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
        ['title' => 'Сертификаты', 'route' => 'admin.certificate.certificates.index', 'icon' => '<rect x="3" y="8" width="18" height="12" rx="2"/><path d="M12 8v4"/><path d="m8 16 2 2 4-4"/>'],
        ['title' => 'Пользователи', 'route' => 'admin.user.users.index', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['title' => 'Роли пользователей', 'route' => 'admin.user.roles.index', 'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
        ['title' => 'Достижения пользователей', 'route' => 'admin.user.userachievements.index', 'icon' => '<polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>'],
        ['title' => 'Прогресс пользователей', 'route' => 'admin.user.userprogresses.index', 'icon' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>'],
        ['title' => 'Результаты пользователей', 'route' => 'admin.user.userresults.index', 'icon' => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>'],
        ['title' => 'Слова пользователей', 'route' => 'admin.user.userwords.index', 'icon' => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
        ['title' => 'Переводы слов', 'route' => 'admin.vocabulary.wordtranslations.index', 'icon' => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
        ['title' => 'Уведомления', 'route' => 'admin.system.notifications.index', 'icon' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'],
        ['title' => 'Профиль', 'route' => 'admin.profile.show', 'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="logo">
        <span class="bolt ph-logo-mark"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l2 3"/><path d="M18 6l-2 3"/><ellipse cx="12" cy="13" rx="7" ry="8"/><circle cx="9" cy="12" r="2.2"/><circle cx="15" cy="12" r="2.2"/><circle cx="9" cy="12" r=".4" fill="#fff" stroke="none"/><circle cx="15" cy="12" r=".4" fill="#fff" stroke="none"/><path d="M11.3 14.5h1.4l-.7 1.2z" fill="#fff" stroke="none"/></svg></span>
        <div><b>Philip<br>Education</b><small>панель управления</small></div>
    </a>

    <nav class="nav">
        @foreach($adminNav as $item)
            @php $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'); @endphp
            <a href="{{ route($item['route']) }}" class="{{ $isActive ? 'active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                {{ $item['title'] }}
            </a>
        @endforeach

        <a href="{{ route('admin.ai') }}" class="{{ request()->routeIs('admin.ai*') ? 'active' : '' }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a4 4 0 0 1 4 4v1a2 2 0 0 1 2 2v1a2 2 0 0 0 2 2 2 2 0 0 1 0 4 2 2 0 0 0-2 2v1a2 2 0 0 1-2 2v1a4 4 0 0 1-8 0v-1a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2 2 2 0 0 1 0-4 2 2 0 0 0 2-2V9a2 2 0 0 1 2-2V6a4 4 0 0 1 4-4z"/>
                <circle cx="12" cy="12" r="2"/>
            </svg>
            AI-генерация
        </a>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Выйти
            </button>
        </form>
    </nav>
</aside>
