<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content\Lesson;
use App\Models\Content\LessonContent;
use App\Models\Exercise\Exercise;
use App\Models\Exercise\ExerciseQuestion;
use App\Models\System\Level;
use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\Vocabulary\Word;
use App\Models\Vocabulary\WordTranslation;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    public function __construct(protected GeminiService $gemini) {}

    public function index()
    {
        return view('admin.ai.index');
    }

    public function generate(Request $request)
    {
        $request->validate(['topic' => 'required|string|max:255']);

        try {
            $pack = $this->gemini->generateLessonPack($request->topic);
        } catch (\Throwable $e) {
            return back()->withErrors(['topic' => $e->getMessage()]);
        }

        try {
            $lesson = DB::transaction(function () use ($pack, $request) {
                $levelId = $this->resolveLevelId($pack['lesson']['level'] ?? null);

                $lesson = Lesson::create([
                    'title' => $pack['lesson']['title'] ?? $request->topic,
                    'level_id' => $levelId,
                    'order_number' => (int) (Lesson::where('level_id', $levelId)->max('order_number') ?? 0) + 1,
                    'estimated_minutes' => 20,
                    'is_published' => false,
                ]);

                if (! empty($pack['lesson']['theory'])) {
                    LessonContent::create([
                        'lesson_id' => $lesson->id,
                        'type' => 'text',
                        'title' => $lesson->title,
                        'content' => nl2br(e($pack['lesson']['theory'])),
                        'order_number' => 1,
                    ]);
                }

                foreach ($pack['words'] ?? [] as $w) {
                    if (empty($w['word'])) {
                        continue;
                    }

                    $word = Word::create([
                        'lesson_id' => $lesson->id,
                        'word' => $w['word'],
                        'difficulty' => $this->difficultyForLevel($pack['lesson']['level'] ?? null),
                    ]);

                    if (! empty($w['translation'])) {
                        WordTranslation::create([
                            'word_id' => $word->id,
                            'language' => 'ru',
                            'translation' => $w['translation'],
                        ]);
                    }
                }

                foreach ($pack['exercises'] ?? [] as $ex) {
                    if (empty($ex['title'])) {
                        continue;
                    }

                    $exercise = Exercise::create([
                        'lesson_id' => $lesson->id,
                        'title' => $ex['title'],
                        'type' => 'fill_blank',
                        'instructions' => 'Complete each task below.',
                    ]);

                    foreach ($ex['questions'] ?? [] as $q) {
                        if (empty($q['question'])) {
                            continue;
                        }

                        ExerciseQuestion::create([
                            'exercise_id' => $exercise->id,
                            'question' => $q['question'],
                            'correct_answer' => $q['answer'] ?? '',
                        ]);
                    }
                }

                if (! empty($pack['test']['questions'])) {
                    $test = Test::create([
                        'lesson_id' => $lesson->id,
                        'title' => $pack['test']['title'] ?? ('Тест: '.$request->topic),
                        'passing_score' => 70,
                        'time_limit' => 10,
                        'is_published' => false,
                    ]);

                    foreach ($pack['test']['questions'] as $q) {
                        $options = $q['options'] ?? [];
                        if (empty($q['question']) || count($options) < 2) {
                            continue;
                        }

                        $testQuestion = TestQuestion::create([
                            'test_id' => $test->id,
                            'question' => $q['question'],
                            'type' => 'single_choice',
                            'points' => 1,
                        ]);

                        $correctIndex = (int) ($q['correct'] ?? 0);

                        foreach ($options as $index => $optionText) {
                            TestAnswer::create([
                                'question_id' => $testQuestion->id,
                                'answer' => $optionText,
                                'is_correct' => $index === $correctIndex,
                            ]);
                        }
                    }
                }

                return $lesson;
            });

            $msg = "✅ Создано: урок «{$lesson->title}», слов: ".count($pack['words'] ?? [])
                .', упражнений: '.count($pack['exercises'] ?? [])
                .', тест готов. Урок создан как черновик — проверьте и опубликуйте его.';

            return back()
                ->with('pack', $pack)
                ->with('status', $msg)
                ->with('lessonId', $lesson->id);
        } catch (\Throwable $e) {
            return back()
                ->with('pack', $pack)
                ->withErrors(['topic' => 'Контент сгенерирован, но не удалось сохранить в БД: '.$e->getMessage()]);
        }
    }

    public function chat(Request $request)
    {
        $request->validate(['question' => 'required|string|max:2000']);

        try {
            $answer = $this->gemini->answerQuestion($request->question);
        } catch (\Throwable $e) {
            return response()->json(['answer' => 'Ошибка: '.$e->getMessage()], 500);
        }

        return response()->json(['answer' => $answer]);
    }

    private function resolveLevelId(?string $levelLabel): ?int
    {
        if (! $levelLabel) {
            return null;
        }

        foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $code) {
            if (str_contains($levelLabel, $code)) {
                return Level::where('code', $code)->value('id');
            }
        }

        return null;
    }

    private function difficultyForLevel(?string $levelLabel): int
    {
        return match (true) {
            str_contains((string) $levelLabel, 'A1') => 2,
            str_contains((string) $levelLabel, 'A2') => 4,
            str_contains((string) $levelLabel, 'B1') => 6,
            str_contains((string) $levelLabel, 'B2') => 7,
            str_contains((string) $levelLabel, 'C1'), str_contains((string) $levelLabel, 'C2') => 9,
            default => 3,
        };
    }
}
