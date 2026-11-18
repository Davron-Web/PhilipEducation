<?php

namespace App\Http\Controllers\Public\LevelCheck;

use App\Http\Controllers\Controller;
use App\Models\LevelCheck\LevelCheck;
use App\Models\System\Level;
use App\Services\CefrPredictionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * «Определи свой уровень по тексту» — быстрая неофициальная оценка через
 * собственную ML-модель (ml/), в отличие от 50-вопросного PlacementController,
 * который официально проставляет users.level_id. Здесь результат только
 * копится в истории, users.level_id не трогаем (см. миграцию level_checks).
 */
class LevelCheckController extends Controller
{
    public function index(): View
    {
        $history = LevelCheck::where('user_id', Auth::id())
            ->with(['predictedLevel', 'rangeLowLevel', 'rangeHighLevel'])
            ->latest()
            ->limit(10)
            ->get();

        return view('public.level-check.index', compact('history'));
    }

    public function check(Request $request, CefrPredictionService $predictor): RedirectResponse
    {
        $data = $request->validate([
            // Верхняя граница — с запасом под 5 длинных предложений; нижняя
            // отсекает совсем пустые/однословные вводы, на которых у модели
            // не будет опоры для оценки уровня.
            'text' => ['required', 'string', 'min:10', 'max:1500'],
        ]);

        try {
            $result = $predictor->predict($data['text']);
        } catch (\Throwable $e) {
            // Тот же принцип, что и у SpeakingBotController: ученику — понятная
            // фраза, в лог — что случилось на самом деле.
            Log::warning('CEFR level check failed: '.$e->getMessage());

            $message = $e->getMessage() === CefrPredictionService::UNAVAILABLE_MESSAGE
                ? $e->getMessage()
                : 'Не удалось определить уровень. Попробуйте ещё раз через минуту.';

            return back()->withInput()->withErrors(['text' => $message]);
        }

        $level = Level::where('code', $result['level'])->first();

        if (! $level) {
            Log::warning("CEFR level check: модель вернула неизвестный код уровня '{$result['level']}'");

            return back()->withInput()->withErrors(['text' => 'Не удалось определить уровень. Попробуйте ещё раз через минуту.']);
        }

        // Диапазон — необязательный (null у обоих, если модель уверена в
        // одном уровне, см. ml/src/range_rule.py); если код от сервиса
        // почему-то не резолвится в Level — тихо считаем, что диапазона
        // нет, а не проваливаем всю проверку из-за необязательной части.
        $rangeLowLevel = $result['range_low'] ? Level::where('code', $result['range_low'])->first() : null;
        $rangeHighLevel = $result['range_high'] ? Level::where('code', $result['range_high'])->first() : null;

        $levelCheck = LevelCheck::create([
            'user_id' => Auth::id(),
            'input_text' => $data['text'],
            'predicted_level_id' => $level->id,
            'range_low_level_id' => $rangeLowLevel?->id,
            'range_high_level_id' => $rangeHighLevel?->id,
            'probabilities' => $result['probabilities'],
            'sentence_count' => $result['sentence_count'],
            'per_sentence' => $result['per_sentence'],
        ]);

        return redirect()->route('tests.level-check.result', $levelCheck);
    }

    public function result(LevelCheck $levelCheck): View
    {
        $this->authorize('view', $levelCheck);

        $levelCheck->load(['predictedLevel', 'rangeLowLevel', 'rangeHighLevel']);

        return view('public.level-check.result', compact('levelCheck'));
    }
}
