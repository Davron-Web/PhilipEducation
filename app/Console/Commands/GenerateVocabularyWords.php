<?php

namespace App\Console\Commands;

use App\Models\Vocabulary\Word;
use App\Models\Vocabulary\WordTranslation;
use App\Services\GeminiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Массовое добавление слов в общий словарь (без привязки к уроку — как и
 * остальные 1162 "самостоятельных" слова уже в базе). Слова генерируются
 * через Gemini батчами по темам, тема пишется в words.category как есть
 * (категория — свободный текст, whitelist в проекте не заведён).
 *
 * Список тем — часть команды, а не конфига: он одноразовый (конкретная
 * партия слов на конкретный запуск), в отличие от config/phil.php, где
 * шаблоны переиспользуются на каждый запрос.
 */
class GenerateVocabularyWords extends Command
{
    protected $signature = 'words:generate {--batch-size=40} {--limit-topics=0 : ограничить число тем (для тестового прогона), 0 = все}';

    protected $description = 'Сгенерировать и добавить партию слов в общий словарь через Gemini';

    /**
     * Тема (пишется в category как есть) => уровень CEFR. Порядок = порядок
     * генерации.
     *
     * Партия 2 (300 слов, 2026-09-16) — темы, которых раньше не было ни в
     * исходных 2468, ни в партии 1 (1000 слов): проверено по фактическому
     * списку distinct(category) в БД перед запуском, чтобы не задваивать
     * то, что уже покрыто. "Животные" — единственное исключение, дополняет
     * уже существующую категорию с тем же названием.
     */
    private const TOPICS = [
        'Животные' => 'A1',
        'Тело человека и анатомия' => 'A2',
        'Сельское хозяйство' => 'A2',
        'Игры и настольные игры' => 'A2',
        'Мифология и фольклор' => 'B1',
        'Экстренные ситуации' => 'B1',
        'Строительство и архитектура' => 'B1',
        'Право и юриспруденция' => 'B2',
        'Военное дело и армия' => 'B2',
        'Искусственный интеллект и данные' => 'C1',
    ];

    public function __construct(private readonly GeminiService $gemini)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $batchSize = (int) $this->option('batch-size');
        $limitTopics = (int) $this->option('limit-topics');
        $totalAdded = 0;
        $totalSkipped = 0;

        $topics = $limitTopics > 0 ? array_slice(self::TOPICS, 0, $limitTopics, true) : self::TOPICS;

        foreach ($topics as $topic => $level) {
            $this->line("→ {$topic} ({$level})...");

            try {
                $words = $this->gemini->generateVocabularyBatch($topic, $level, $batchSize);
            } catch (\Throwable $e) {
                $this->error("  Пропущено — ошибка Gemini: {$e->getMessage()}");
                report($e);

                continue;
            }

            $difficulty = $this->difficultyForLevel($level);
            $seenInBatch = [];
            $added = 0;

            DB::transaction(function () use ($words, $topic, $difficulty, &$seenInBatch, &$added, &$totalSkipped) {
                foreach ($words as $row) {
                    $wordText = trim((string) $row['word']);
                    $key = mb_strtolower($wordText);

                    // Модель иногда повторяется внутри одного ответа —
                    // сверяемся только в пределах этого батча, не со всей
                    // таблицей: дубли слов в проекте — штатная ситуация
                    // (см. WordController::index(), там их даже считают).
                    if ($wordText === '' || isset($seenInBatch[$key])) {
                        $totalSkipped++;

                        continue;
                    }
                    $seenInBatch[$key] = true;

                    $word = Word::create([
                        'lesson_id' => null,
                        'word' => $wordText,
                        'transcription' => trim((string) ($row['transcription'] ?? '')) ?: null,
                        'example' => trim((string) ($row['example'] ?? '')) ?: null,
                        'difficulty' => $difficulty,
                        'category' => $topic,
                    ]);

                    if (! empty($row['translation'])) {
                        WordTranslation::create([
                            'word_id' => $word->id,
                            'language' => 'ru',
                            'translation' => trim((string) $row['translation']),
                        ]);
                    }

                    $added++;
                }
            });

            $this->info("  добавлено: {$added}");
            $totalAdded += $added;
        }

        $this->newLine();
        $this->info("Итого добавлено слов: {$totalAdded} (пропущено дублей внутри батчей: {$totalSkipped})");

        return self::SUCCESS;
    }

    /** Та же шкала, что и Admin\AiController::difficultyForLevel() — держим сложность слов в одной логике с генерацией уроков. */
    private function difficultyForLevel(string $levelLabel): int
    {
        return match (true) {
            str_contains($levelLabel, 'A1') => 2,
            str_contains($levelLabel, 'A2') => 4,
            str_contains($levelLabel, 'B1') => 6,
            str_contains($levelLabel, 'B2') => 7,
            str_contains($levelLabel, 'C1'), str_contains($levelLabel, 'C2') => 9,
            default => 3,
        };
    }
}
