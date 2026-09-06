<?php

use App\Models\Test\Test;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;

it('показывает варианты в порядке sort_order, а не вставки', function () {
    $question = TestQuestion::factory()->singleChoice()->create(['test_id' => Test::factory()->create()->id]);

    // Вставляем правильный первым — так делали сидеры, из-за чего ответом
    // почти всегда оказывался вариант А.
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'верно', 'is_correct' => true, 'sort_order' => 3]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'мимо 1', 'is_correct' => false, 'sort_order' => 0]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'мимо 2', 'is_correct' => false, 'sort_order' => 1]);
    TestAnswer::create(['question_id' => $question->id, 'answer' => 'мимо 3', 'is_correct' => false, 'sort_order' => 2]);

    $order = $question->fresh()->answers->pluck('answer')->all();

    expect($order)->toBe(['мимо 1', 'мимо 2', 'мимо 3', 'верно']);
});

it('не ставит правильный ответ всегда первым в данных', function () {
    // Проверка самих данных: если правильный вариант окажется первым более
    // чем в половине вопросов, тест снова можно проходить угадыванием.
    $positions = DB::select("
        select pos, count(*) as n from (
            select row_number() over (partition by question_id order by sort_order, id) as pos, is_correct
            from test_answers
        ) t
        where t.is_correct = 1
        group by pos
    ");

    $total = array_sum(array_column($positions, 'n'));

    if ($total === 0) {
        expect(true)->toBeTrue();

        return;
    }

    $first = 0;
    foreach ($positions as $row) {
        if ((int) $row->pos === 1) {
            $first = (int) $row->n;
        }
    }

    expect($first / $total)->toBeLessThan(0.5);
});
