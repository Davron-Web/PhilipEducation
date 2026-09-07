<?php

use App\Models\Content\GrammarTopic;
use App\Models\Content\Lesson;
use App\Models\Test\Test;
use App\Models\Test\TestQuestion;
use App\Services\QuestionTopicResolver;

function questionIn(string $testTitle, ?string $grammarTitle = null, string $text = 'Choose the correct form.'): TestQuestion
{
    $lesson = Lesson::factory()->create([
        'grammar_topic_id' => $grammarTitle
            ? GrammarTopic::factory()->create(['title' => $grammarTitle])->id
            : null,
    ]);

    $test = Test::factory()->create(['title' => $testTitle, 'lesson_id' => $lesson->id]);

    return TestQuestion::factory()->singleChoice()->create([
        'test_id' => $test->id,
        'question' => $text,
    ]);
}

it('берёт тему из раздела грамматики урока', function () {
    $question = questionIn('Test: Unit 4', 'Present Perfect');

    expect(app(QuestionTopicResolver::class)->resolve($question))->toBe('present-perfect');
});

it('различает present perfect и present simple', function () {
    // Порядок проверки ключевых слов важен: «present perfect» содержит
    // «present», и при обратном порядке всё уходило бы в present-simple.
    expect(app(QuestionTopicResolver::class)->resolve(questionIn('Test: Present Perfect Continuous')))
        ->toBe('present-perfect-continuous');

    expect(app(QuestionTopicResolver::class)->resolve(questionIn('Test: Present Simple')))
        ->toBe('present-simple');
});

it('относит вопросы о значении слова к словарю', function () {
    $question = questionIn('Test: There Is / There Are', null, "What does 'fridge' mean?");

    expect(app(QuestionTopicResolver::class)->resolve($question))->toBe('vocabulary');
});

it('делает тему из названия теста, когда ключевых слов нет', function () {
    $question = questionIn('Test: There Is / There Are');

    // «Test: » в начале только мешает — это не часть темы.
    expect(app(QuestionTopicResolver::class)->resolve($question))->toBe('there-is-there-are');
});

it('не выдумывает тему, когда данных нет', function () {
    // Вопрос без теста: пустая тема честнее наугад присвоенной, потому что
    // на теме строится статистика ошибок.
    $question = new TestQuestion(['question' => 'Choose the correct form.']);

    expect(app(QuestionTopicResolver::class)->resolve($question))->toBeNull();
});

it('сохраняет тему у вопроса', function () {
    $question = TestQuestion::factory()->singleChoice()->create([
        'test_id' => Test::factory()->create()->id,
        'topic' => 'articles',
    ]);

    expect($question->fresh()->topic)->toBe('articles');
});
