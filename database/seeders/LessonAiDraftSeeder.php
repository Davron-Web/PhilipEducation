<?php

namespace Database\Seeders;

use App\Models\Content\Lesson;
use App\Models\Content\LessonContent;
use App\Models\Exercise\Exercise;
use App\Models\Exercise\ExerciseQuestion;
use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use App\Models\Vocabulary\Word;
use Illuminate\Database\Seeder;

/**
 * Единственный урок, обнаруженный migrate:fresh --seed на пустой базе как
 * не покрытый ни одним сидером: черновик «Приветствия в английском языке»,
 * собранный через AI-генератор в админке (App\Http\Controllers\Admin\
 * AiController) и оставленный неопубликованным (is_published=false у
 * урока и у теста — так их создаёт сам генератор).
 *
 * Публикуем его здесь тем же черновиком, каким он и остаётся на боевой
 * базе — воспроизводим, а не публикуем, потому что WordController::index()
 * не фильтрует слова по статусу публикации урока: пять слов этого
 * черновика уже видны в публичном словаре, и без этого сидера свежая
 * установка отставала бы от боевой базы ровно на них.
 *
 * Идемпотентен по заголовку урока — безопасно перезапускать.
 */
class LessonAiDraftSeeder extends Seeder
{
    public function run(): void
    {
        if (Lesson::where('title', 'Приветствия в английском языке (Greetings)')->exists()) {
            $this->command?->info('LessonAiDraftSeeder: урок уже существует, пропущено.');

            return;
        }

        $lesson = Lesson::create([
            'level_id' => 1,
            'title' => 'Приветствия в английском языке (Greetings)',
            'order_number' => (int) (Lesson::where('level_id', 1)->max('order_number') ?? 0) + 1,
            'estimated_minutes' => 20,
            'is_published' => false,
        ]);

        LessonContent::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'title' => $lesson->title,
            'content' => "Приветствия в английском языке делятся на формальные и неформальные. ".
                "Формальные используются в деловом общении или с незнакомыми людьми: 'Hello' (Здравствуйте), ".
                "'Good morning' (Доброе утро), 'Good afternoon' (Добрый день), 'Good evening' (Добрый вечер). ".
                "Неформальные используются с друзьями и близкими: 'Hi' (Привет), 'Hey' (Привет). ".
                "Для вопроса 'Как дела?' чаще всего используется 'How are you?', а ответом может служить ".
                "'I am fine, thank you' (У меня все хорошо, спасибо). При прощании говорят 'Goodbye' ".
                "(До свидания), 'Bye' (Пока) или 'See you later' (Увидимся позже).",
            'order_number' => 1,
        ]);

        foreach ($this->words() as [$word, $ru]) {
            $w = Word::create([
                'lesson_id' => $lesson->id,
                'word' => $word,
                'difficulty' => 2,
            ]);
            $w->translations()->create(['language' => 'ru', 'translation' => $ru]);
        }

        foreach ($this->exercises() as $title => $questions) {
            $exercise = Exercise::create([
                'lesson_id' => $lesson->id,
                'title' => $title,
                'type' => 'fill_blank',
                'instructions' => 'Complete each task below.',
            ]);

            foreach ($questions as [$question, $answer]) {
                ExerciseQuestion::create([
                    'exercise_id' => $exercise->id,
                    'question' => $question,
                    'correct_answer' => $answer,
                ]);
            }
        }

        $test = Test::create([
            'lesson_id' => $lesson->id,
            'title' => 'Тест: Приветствия на английском',
            'passing_score' => 70,
            'time_limit' => 10,
            'is_published' => false,
        ]);

        foreach ($this->testQuestions() as [$question, $options]) {
            $q = TestQuestion::create([
                'test_id' => $test->id,
                'question' => $question,
                'type' => 'single_choice',
                'points' => 1,
            ]);

            // Порядок вариантов воспроизведён как в исходном черновике
            // (sort_order 0-3), а не перемешан заново.
            foreach ($options as $position => [$answer, $isCorrect]) {
                TestAnswer::create([
                    'question_id' => $q->id,
                    'answer' => $answer,
                    'is_correct' => $isCorrect,
                    'sort_order' => $position,
                ]);
            }
        }

        $this->command?->info(sprintf(
            'LessonAiDraftSeeder: +1 урок (черновик), +%d слов, +%d упражнений, +1 тест.',
            count($this->words()),
            count($this->exercises())
        ));
    }

    /** @return array<int, array{0: string, 1: string}> */
    private function words(): array
    {
        return [
            ['Hello', 'Здравствуйте / Привет'],
            ['Hi', 'Привет'],
            ['Good morning', 'Доброе утро'],
            ['How are you?', 'Как дела?'],
            ['Goodbye', 'До свидания'],
            ['See you later', 'Увидимся позже'],
        ];
    }

    /** @return array<string, array<int, array{0: string, 1: string}>> */
    private function exercises(): array
    {
        return [
            'Упражнение 1. Перевод фраз' => [
                ["Переведите на английский: 'Доброе утро'", 'Good morning'],
                ["Переведите на английский: 'Как дела?'", 'How are you?'],
                ["Переведите на английский: 'До свидания'", 'Goodbye'],
            ],
            'Упражнение 2. Вставьте пропущенное слово' => [
                ['Good _____! (Доброе утро!)', 'morning'],
                ['See you _____! (Увидимся позже!)', 'later'],
                ['How _____ you? (Как дела?)', 'are'],
            ],
        ];
    }

    /**
     * @return array<int, array{0: string, 1: array<int, array{0: string, 1: bool}>}>
     */
    private function testQuestions(): array
    {
        return [
            ["Как сказать 'Привет' в неформальной обстановке?", [
                ['Goodbye', false], ['Good evening', false], ['Good morning', false], ['Hi', true],
            ]],
            ["Что означает фраза 'Good evening'?", [
                ['Доброй ночи', false], ['Добрый день', false], ['Добрый вечер', true], ['Доброе утро', false],
            ]],
            ["Какой вариант ответа подходит на вопрос 'How are you?'?", [
                ['Good night', false], ['See you later', false], ['I am fine, thank you', true], ['Hello', false],
            ]],
            ['Какая фраза используется для прощания?', [
                ['Good morning', false], ['Nice to meet you', false], ['See you later', true], ['How are you?', false],
            ]],
            ["Переведите фразу 'Nice to meet you':", [
                ['До свидания', false], ['Доброе утро', false], ['Как дела?', false], ['Приятно познакомиться', true],
            ]],
        ];
    }
}
