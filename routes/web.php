<?php

use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\Billing\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\Billing\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\Billing\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\Billing\SubscriptionController as AdminBillingSubscriptionController;

use App\Http\Controllers\ProfileController as BreezeProfileController;
use App\Http\Controllers\Admin\Book\BookController as AdminBookController;
use App\Http\Controllers\Admin\Certificate\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\Content\GrammarTopicController as AdminGrammarTopicController;
use App\Http\Controllers\Admin\Content\LessonContentController as AdminLessonContentController;
use App\Http\Controllers\Admin\Content\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Exercise\ExerciseController as AdminExerciseController;
use App\Http\Controllers\Admin\Exercise\ExerciseQuestionController as AdminExerciseQuestionController;
use App\Http\Controllers\Admin\Gamification\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\Gamification\TitleController as AdminTitleController;
use App\Http\Controllers\Admin\Gamification\StudyStatisticController as AdminStudyStatisticController;
use App\Http\Controllers\Admin\System\LevelController as AdminLevelController;
use App\Http\Controllers\Admin\System\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\Test\TestAnswerController as AdminTestAnswerController;
use App\Http\Controllers\Admin\Test\TestController as AdminTestController;
use App\Http\Controllers\Admin\Test\TestQuestionController as AdminTestQuestionController;
use App\Http\Controllers\Admin\User\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\User\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\User\UserAchievementController as AdminUserAchievementController;
use App\Http\Controllers\Admin\User\UserController as AdminUserController;
use App\Http\Controllers\Admin\User\UserSubscriptionController as AdminUserSubscriptionController;
use App\Http\Controllers\Admin\User\UserProgressController as AdminUserProgressController;
use App\Http\Controllers\Admin\User\UserResultController as AdminUserResultController;
use App\Http\Controllers\Admin\User\UserWordController as AdminUserWordController;
use App\Http\Controllers\Admin\Vocabulary\ExpressionController as AdminExpressionController;
use App\Http\Controllers\Admin\Vocabulary\WordController as AdminWordController;
use App\Http\Controllers\Admin\Vocabulary\WordTranslationController as AdminWordTranslationController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\Public\Book\BookController as PublicBookController;
use App\Http\Controllers\Public\Content\GrammarTopicController as PublicGrammarTopicController;
use App\Http\Controllers\Public\Content\LessonController as PublicLessonController;
use App\Http\Controllers\Public\Exercise\ExerciseController as PublicExerciseController;
use App\Http\Controllers\Public\Gamification\AchievementController as PublicAchievementController;
use App\Http\Controllers\Public\Ielts\IeltsController as PublicIeltsController;
use App\Http\Controllers\Public\Ielts\IeltsHubController as PublicIeltsHubController;
use App\Http\Controllers\Public\Ielts\IeltsListeningController as PublicIeltsListeningController;
use App\Http\Controllers\Public\Ielts\IeltsReadingController as PublicIeltsReadingController;
use App\Http\Controllers\Public\Ielts\IeltsSpeakingController as PublicIeltsSpeakingController;
use App\Http\Controllers\Public\Ielts\SpeakingBotController;
use App\Http\Controllers\Public\System\NotificationController as PublicNotificationController;
use App\Http\Controllers\Public\Test\TestController as PublicTestController;
use App\Http\Controllers\Public\User\ProfileController as PublicProfileController;
use App\Http\Controllers\Public\Vocabulary\ExpressionController as PublicExpressionController;
use App\Http\Controllers\Public\Vocabulary\WordController as PublicWordController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Billing\SandboxController;
use App\Http\Controllers\Billing\SubscriptionController;
use App\Http\Middleware\SetLocale;
use App\Models\Content\Lesson;
use App\Models\System\Level;
use App\Models\User;
use App\Models\Vocabulary\Word;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root (Публичная главная страница)
|--------------------------------------------------------------------------
*/
// Карта сайта — вне групп auth, её должен видеть поисковый робот.
Route::get('/sitemap.xml', App\Http\Controllers\Public\System\SitemapController::class)->name('sitemap');

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role_id == 1) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    // Реальные данные для гостевой главной (уровни + честные цифры платформы,
    // без выдуманных чисел).
    $levels = Level::whereIn('code', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])
        ->orderBy('code')
        ->get()
        ->map(fn ($level) => [
            'code' => $level->code,
            'name' => $level->name,
            'description' => $level->description ?: 'Материалы уровня '.$level->code.'.',
            'lessons_count' => Lesson::where('level_id', $level->id)->where('is_published', true)->count(),
        ]);

    $totalLessons = Lesson::where('is_published', true)->count();
    $totalWords = Word::count();
    $totalUsers = User::count();

    return view('public.home', compact('levels', 'totalLessons', 'totalWords', 'totalUsers'));
})->name('home');

/*
|--------------------------------------------------------------------------
| Язык интерфейса
|--------------------------------------------------------------------------
*/
Route::get('/locale/{locale}', function (string $locale) {
    if (array_key_exists($locale, SetLocale::SUPPORTED)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Подписка и оплата
|--------------------------------------------------------------------------
*/
Route::get('/plans', [SubscriptionController::class, 'plans'])->name('billing.plans');
Route::get('/billing/return', [SubscriptionController::class, 'return'])->name('billing.return');

// Вебхук приходит от банка, а не из браузера: без auth и без CSRF.
Route::post('/billing/webhook', [SubscriptionController::class, 'webhook'])
    ->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('billing.webhook');

Route::middleware('auth')->group(function () {
    Route::post('/billing/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('billing.checkout');
    Route::post('/billing/cancel', [SubscriptionController::class, 'cancel'])->name('billing.cancel');
});

// Тестовая «страница банка» — только вне production.
if (! app()->environment('production')) {
    Route::get('/billing/sandbox/{payment}', [SandboxController::class, 'show'])
        ->middleware('signed')->name('billing.sandbox');
    Route::post('/billing/sandbox/{payment}', [SandboxController::class, 'pay'])->name('billing.sandbox.pay');
}

/*
|--------------------------------------------------------------------------
| Public Lessons & User Area
|--------------------------------------------------------------------------
*/
Route::get('/public/lessons', [PublicLessonController::class, 'index'])->name('public.lessons.index');
Route::get('/public/lessons/{id}', [PublicLessonController::class, 'show'])->name('public.lessons.show');
Route::middleware('auth')->post('/public/lessons/{id}/complete', [PublicLessonController::class, 'complete'])->name('public.lessons.complete');

Route::get('/user/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('user.dashboard');

Route::get('/dashboard', function () {
    if (auth()->user()->role_id == 1) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
})->middleware(['auth'])->name('dashboard');

// Каждый ответ Phil — платный запрос к Gemini, поэтому раздел под подпиской.
Route::post('/assistant/chat', [AssistantController::class, 'chat'])
    ->middleware(['auth', 'subscribed:phil', 'throttle:20,1'])
    ->name('assistant.chat');

Route::middleware(['auth'])->group(function () {
    Route::prefix('lessons')->name('lessons.')->group(function () {
        Route::get('/', [PublicLessonController::class, 'index'])->name('index');
        Route::get('/{id}', [PublicLessonController::class, 'show'])->name('show');
    });

    Route::prefix('exercises')->name('exercises.')->group(function () {
        Route::get('/', [PublicExerciseController::class, 'index'])->name('index');
        Route::get('/{exercise}', [PublicExerciseController::class, 'show'])->name('show');
        Route::post('/{exercise}/check', [PublicExerciseController::class, 'check'])->name('check');
    });

    Route::prefix('grammartopics')->name('grammartopics.')->group(function () {
        Route::get('/', [PublicGrammarTopicController::class, 'index'])->name('index');
        Route::get('/{topic}', [PublicGrammarTopicController::class, 'show'])->name('show');
    });

    Route::prefix('words')->name('words.')->group(function () {
        Route::get('/', [PublicWordController::class, 'index'])->name('index');
        // До /{word}: иначе 'review' будет принят за слово.
        Route::get('/review', [PublicWordController::class, 'review'])->name('review');
        Route::post('/{word}/review', [PublicWordController::class, 'reviewAnswer'])->name('review.answer');
        Route::post('/', [PublicWordController::class, 'store'])->name('store');
        Route::get('/{word}', [PublicWordController::class, 'show'])->name('show');
        Route::post('/{word}/progress', [PublicWordController::class, 'markProgress'])->name('progress');
    });

    Route::prefix('expressions')->name('expressions.')->group(function () {
        Route::get('/', [PublicExpressionController::class, 'index'])->name('index');
        Route::post('/', [PublicExpressionController::class, 'store'])->name('store');
        Route::get('/{expression}', [PublicExpressionController::class, 'show'])->name('show');
        Route::post('/{expression}/progress', [PublicExpressionController::class, 'markProgress'])->name('progress');
        Route::post('/{expression}/practice', [PublicExpressionController::class, 'recordPractice'])->name('practice');
    });

    Route::prefix('tests')->name('tests.')->group(function () {
        Route::get('/', [PublicTestController::class, 'index'])->name('index');
        Route::get('/{id}', [PublicTestController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [PublicTestController::class, 'submit'])->name('submit');
        Route::get('/attempts/{attempt}', [PublicTestController::class, 'result'])->name('result');
    });

    Route::prefix('books')->name('books.')->middleware('subscribed:books')->group(function () {
        Route::get('/', [PublicBookController::class, 'index'])->name('index');
        Route::get('/{book}/read', [PublicBookController::class, 'read'])->name('read');
        Route::post('/{book}/progress', [PublicBookController::class, 'saveProgress'])->name('progress');
    });

    Route::prefix('ielts')->name('ielts.')->middleware('subscribed:ielts')->group(function () {
        Route::get('/', [PublicIeltsHubController::class, 'index'])->name('index');

        Route::prefix('writing')->name('writing.')->group(function () {
            Route::get('/', [PublicIeltsController::class, 'index'])->name('index');
            Route::get('/{task}', [PublicIeltsController::class, 'show'])->name('show');
            Route::post('/{task}/submit', [PublicIeltsController::class, 'submit'])->name('submit');
        });

        Route::prefix('reading')->name('reading.')->group(function () {
            Route::get('/', [PublicIeltsReadingController::class, 'index'])->name('index');
            Route::get('/{passage}', [PublicIeltsReadingController::class, 'show'])->name('show');
            Route::post('/{passage}/submit', [PublicIeltsReadingController::class, 'submit'])->name('submit');
        });

        Route::prefix('listening')->name('listening.')->group(function () {
            Route::get('/', [PublicIeltsListeningController::class, 'index'])->name('index');
            Route::get('/{passage}', [PublicIeltsListeningController::class, 'show'])->name('show');
            Route::post('/{passage}/submit', [PublicIeltsListeningController::class, 'submit'])->name('submit');
        });

        Route::prefix('speaking')->name('speaking.')->group(function () {
            Route::get('/', [PublicIeltsSpeakingController::class, 'index'])->name('index');

            // Разговорный бот — до /{card}, иначе 'bot' примут за карточку.
            Route::get('/bot', [SpeakingBotController::class, 'room'])->name('bot');
            Route::post('/bot/reply', [SpeakingBotController::class, 'reply'])
                ->middleware('throttle:40,1')->name('bot.reply');
            Route::post('/bot/report', [SpeakingBotController::class, 'report'])
                ->middleware('throttle:10,1')->name('bot.report');

            Route::get('/{card}', [PublicIeltsSpeakingController::class, 'show'])->name('show');
        });
    });

    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::get('/', [PublicProfileController::class, 'index'])->name('index');
        Route::get('/edit', [PublicProfileController::class, 'edit'])->name('edit');
        Route::put('/', [PublicProfileController::class, 'update'])->name('update');
        Route::get('/stats', [PublicProfileController::class, 'stats'])->name('stats');

        // Удаление аккаунта уже реализовано в контроллере Breeze — там верная
        // последовательность: подтверждение паролем, выход, удаление, сброс
        // сессии. Своя копия только добавила бы шанс что-то из этого забыть.
        Route::delete('/', [BreezeProfileController::class, 'destroy'])->name('destroy');

        Route::get('/{user}', [PublicProfileController::class, 'show'])->name('show');
    });

    Route::get('/achievements', [PublicAchievementController::class, 'index'])->name('achievements.index');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [PublicNotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [PublicNotificationController::class, 'show'])->name('show');
        Route::post('/mark-all-read', [PublicNotificationController::class, 'markAllRead'])->name('markAllRead');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Поиск (заглушка, чтобы форма в шаблоне не выдавала ошибку 404)
        Route::get('/search', function () {
            return redirect()->back();
        })->name('search');

        // Content
        Route::resource('content/lessons', AdminLessonController::class)->names('content.lessons');
        Route::resource('content/grammartopics', AdminGrammarTopicController::class)->names('content.grammartopics');
        Route::resource('content/lessoncontents', AdminLessonContentController::class)->names('content.lessoncontents');

        // Vocabulary
        Route::resource('vocabulary/words', AdminWordController::class)->names('vocabulary.words');
        Route::resource('vocabulary/word-translations', AdminWordTranslationController::class)->names('vocabulary.wordtranslations');
        Route::resource('vocabulary/expressions', AdminExpressionController::class)->names('vocabulary.expressions');

        // Exercise
        Route::resource('exercise/exercises', AdminExerciseController::class)->names('exercise.exercises');
        Route::resource('exercise/exercisequestions', AdminExerciseQuestionController::class)->names('exercise.exercisequestions');

        // Users & Roles
        Route::resource('user/users', AdminUserController::class)->names('user.users');

        // Премиум в подарок — доступ без оплаты.
        Route::post('user/users/{user}/premium', [AdminUserSubscriptionController::class, 'grant'])->name('user.users.premium.grant');
        Route::delete('user/users/{user}/premium', [AdminUserSubscriptionController::class, 'revoke'])->name('user.users.premium.revoke');
        Route::resource('user/roles', AdminRoleController::class)->names('user.roles');

        // User Progress & Results
        Route::resource('user/userachievements', AdminUserAchievementController::class)->names('user.userachievements');
        Route::resource('user/userprogress', AdminUserProgressController::class)->names('user.userprogresses');
        Route::resource('user/userresults', AdminUserResultController::class)->names('user.userresults');
        Route::resource('user/userwords', AdminUserWordController::class)->names('user.userwords');

        // Gamification
        Route::resource('gamification/achievements', AdminAchievementController::class)->names('gamification.achievements');
        Route::resource('gamification/titles', AdminTitleController::class)->names('gamification.titles');

        // Биллинг: тарифы правят, остальное только смотрят — платежи и счета
        // отражают операции провайдера, руками их менять нельзя.
        Route::resource('billing/plans', AdminPlanController::class)
            ->except('show')
            ->names('billing.plans');

        Route::get('billing/subscriptions', [AdminBillingSubscriptionController::class, 'index'])->name('billing.subscriptions.index');
        Route::get('billing/subscriptions/{subscription}', [AdminBillingSubscriptionController::class, 'show'])->name('billing.subscriptions.show');
        Route::post('billing/subscriptions/{subscription}/cancel', [AdminBillingSubscriptionController::class, 'cancel'])->name('billing.subscriptions.cancel');

        Route::get('billing/payments', [AdminPaymentController::class, 'index'])->name('billing.payments.index');
        Route::get('billing/payments/{payment}', [AdminPaymentController::class, 'show'])->name('billing.payments.show');

        Route::get('billing/invoices', [AdminInvoiceController::class, 'index'])->name('billing.invoices.index');
        Route::get('billing/invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('billing.invoices.show');
        Route::resource('gamification/study-statistics', AdminStudyStatisticController::class)->names('gamification.studystatistics');

        // System
        Route::resource('system/levels', AdminLevelController::class)->names('system.levels');
        Route::resource('system/notifications', AdminNotificationController::class)->names('system.notifications');
        Route::patch('system/notifications/{notification}/mark-read', [AdminNotificationController::class, 'markAsRead'])
            ->name('system.notifications.mark-read');

        // Tests
        Route::resource('test/tests', AdminTestController::class)->names('test.tests');
        Route::resource('test/testquestions', AdminTestQuestionController::class)->names('test.testquestions');
        Route::resource('test/testanswers', AdminTestAnswerController::class)->names('test.testanswers');

        // Certificates
        Route::resource('certificate/certificates', AdminCertificateController::class)->names('certificate.certificates');

        // Books
        Route::resource('book/books', AdminBookController::class)->names('book.books');

        // Admin Profile
        Route::get('profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');

        // AI (исправлено имя маршрута на 'ai' вместо 'ai.index')
        Route::get('ai', [AiController::class, 'index'])->name('ai');
        Route::post('ai/generate', [AiController::class, 'generate'])->name('ai.generate');
        Route::post('ai/chat', [AiController::class, 'chat'])->name('ai.chat');

        // Theme Toggle
        Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
