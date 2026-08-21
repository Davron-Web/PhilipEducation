<aside class="sidebar-nav-wrapper">
    <div class="navbar-logo">
        <a href="#">
            <img src="{{ asset('admin/assets/images/logo/logo.svg') }}" alt="logo" />
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul>

            <!-- достижения -->
            <li class="nav-item">
                <a href="{{ route('admin.gamification.achievements.index') }}">
                    <span class="text">достижения</span>
                </a>
            </li>


            <!-- упражнения -->
            <li class="nav-item">
                <a href="{{ route('admin.exercise.exercises.index') }}">
                    <span class="text">упражнения</span>
                </a>
            </li>


            <!-- вопросы упражнений -->
            <li class="nav-item">
                <a href="{{ route('admin.exercise.exercisequestions.index') }}">
                    <span class="text">вопросы упражнений</span>
                </a>
            </li>


            <!-- грамматика -->
            <li class="nav-item">
                <a href="{{ route('admin.content.grammartopics.index') }}">
                    <span class="text">темы грамматики</span>
                </a>
            </li>


            <!-- уроки -->
            <li class="nav-item">
                <a href="{{ route('admin.content.lessons.index') }}">
                    <span class="text">уроки</span>
                </a>
            </li>


            <!-- уведомления -->
            <li class="nav-item">
                <a href="{{ route('admin.system.notifications.index') }}">
                    <span class="text">уведомления</span>
                </a>
            </li>


            <!-- роли -->
            <li class="nav-item">
                <a href="{{ route('admin.user.roles.index') }}">
                    <span class="text">роли</span>
                </a>
            </li>


            <!-- статистика обучения -->
            <li class="nav-item">
                <a href="{{ route('admin.gamification.studystatistics.index') }}">
                    <span class="text">статистика обучения</span>
                </a>
            </li>


            <!-- тесты -->
            <li class="nav-item">
                <a href="{{ route('admin.test.tests.index') }}">
                    <span class="text">тесты</span>
                </a>
            </li>


            <!-- ответы тестов -->
            <li class="nav-item">
                <a href="{{ route('admin.test.testanswers.index') }}">
                    <span class="text">ответы тестов</span>
                </a>
            </li>


            <!-- пользователи -->
            <li class="nav-item">
                <a href="{{ route('admin.user.users.index') }}">
                    <span class="text">пользователи</span>
                </a>
            </li>


            <!-- достижения пользователей -->
            <li class="nav-item">
                <a href="{{ route('admin.user.userachievements.index') }}">
                    <span class="text">достижения пользователей</span>
                </a>
            </li>


            <!-- прогресс пользователей -->
            <li class="nav-item">
                <a href="{{ route('admin.user.userprogresses.index') }}">
                    <span class="text">прогресс пользователей</span>
                </a>
            </li>


            <!-- результаты пользователей -->
            <li class="nav-item">
                <a href="{{ route('admin.user.userresults.index') }}">
                    <span class="text">результаты пользователей</span>
                </a>
            </li>


            <!-- слова -->
            <li class="nav-item">
                <a href="{{ route('admin.vocabulary.words.index') }}">
                    <span class="text">слова</span>
                </a>
            </li>


            <!-- переводы слов -->
            <li class="nav-item">
                <a href="{{ route('admin.vocabulary.wordtranslations.index') }}">
                    <span class="text">переводы слов</span>
                </a>
            </li>




        </ul>
    </nav>
    <div class="promo-box">
        <div class="promo-icon">
            <img
                class="mx-auto"
                src="{{ asset('admin/assets/images/logo/logo-icon-big.svg') }}"
                alt="Logo"
            >
        </div>

        <h3>English Learning System</h3>

        <p>
            Admin Panel
        </p>

        <a href="#" class="main-btn primary-btn btn-hover">
            Dashboard
        </a>
    </div>
</aside>
