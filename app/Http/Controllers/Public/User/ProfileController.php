<?php

namespace App\Http\Controllers\Public\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\User\UpdateUserAvatarRequest;
use App\Http\Requests\Public\User\UpdateUserProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Services\TopicStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function me(): JsonResponse
    {
        $user = Auth::user()->load([
            'role',
            'level',
            'progress.lesson',
            'results.test',
            'words.translations',
            'achievements',
            'certificates',
            'comments',
        ]);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Current user's own profile overview.
     */
    public function index(): View
    {
        $user = Auth::user()->load(['role', 'level']);
        $stats = $this->statsFor($user);
        $topicStats = app(TopicStatsService::class)->forUser($user);

        return view('public.profiles.index', compact('user', 'stats', 'topicStats'));
    }

    public function edit(): View
    {
        $user = Auth::user();

        return view('public.profiles.edit', compact('user'));
    }

    public function show(Request $request, User $user): View|JsonResponse
    {
        // Без этой проверки любой ученик читал чужой профиль, подставив id в
        // адрес: HTML отдавал чужую статистику, а JSON — ещё и email вместе
        // со всей историей обучения, результатами тестов и сертификатами.
        $this->authorize('view', $user);

        $user->load(['role', 'level']);
        $stats = $this->statsFor($user);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $user->load(['progress.lesson', 'results.test', 'words', 'achievements', 'certificates']),
            ]);
        }

        return view('public.profiles.show', compact('user', 'stats'));
    }

    public function update(UpdateUserProfileRequest $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $data = $request->safe()->only(['name', 'name_latin', 'email']);
        // Непроставленный чекбокс браузер вообще не отправляет, поэтому
        // boolean() с явным false по умолчанию — то, что нужно.
        $data['show_in_leaderboard'] = $request->boolean('show_in_leaderboard');

        // Подтверждение почты отключено, поэтому смена адреса больше не
        // сбрасывает email_verified_at и не шлёт письмо: иначе человек
        // остался бы с неподтверждённым адресом навсегда — подтвердить
        // его сейчас нечем. Домен проверяется правилом email:rfc,dns при
        // сохранении, и выдуманный адрес сюда не пройдёт.

        $user->forceFill($data)->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => $user->fresh(),
            ]);
        }

        return redirect()->route('profiles.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Выбор аватара из готового набора (галерея в настройках профиля) —
     * отдельная форма/маршрут от update(): один клик по картинке сохраняет
     * сразу, без остальных полей профиля рядом.
     */
    public function updateAvatar(UpdateUserAvatarRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Если раньше был загружен вручную настоящий файл (старая функция,
        // сейчас убрана из UI) — подчищаем его, иначе в storage копятся
        // файлы, на которые больше никто не ссылается.
        if ($user->avatar
            && ! str_starts_with($user->avatar, 'http://')
            && ! str_starts_with($user->avatar, 'https://')
            && ! in_array($user->avatar, config('avatars.files', []), true)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->forceFill(['avatar' => $request->avatar])->save();

        return redirect()->route('profiles.edit')->with('success', 'Аватар обновлён.');
    }

    public function stats(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'data' => $this->statsFor($user),
        ]);
    }

    /**
     * @return array{level: mixed, points: int, words_learned: int, lessons_completed: int, tests_passed: int, achievements_count: int, certificates_count: int}
     */
    private function statsFor(User $user): array
    {
        return [
            'level' => $user->level,
            'points' => $user->points,
            'words_learned' => $user->words()->count(),
            'lessons_completed' => $user->completedLessonsCount(),
            'tests_passed' => $user->results()->where('passed', true)->count(),
            'achievements_count' => $user->achievements()->count(),
            'certificates_count' => $user->certificates()->count(),
        ];
    }
}
