<?php

namespace App\Services;

use App\Models\Test\TestQuestion;
use Illuminate\Support\Str;

/**
 * Определяет тему вопроса.
 *
 * Тема берётся из того, что уже известно о вопросе, а не выдумывается:
 * сначала раздел грамматики урока, к которому привязан тест, затем название
 * самого теста. Если ничего не подошло — null: пустая тема честнее, чем
 * наугад присвоенная, потому что на теме строится статистика ошибок.
 */
class QuestionTopicResolver
{
    /**
     * Ключевые слова в названии теста или урока, по которым тема
     * распознаётся однозначно. Проверяются в порядке от длинного к
     * короткому, иначе «present perfect» попал бы в «present simple».
     */
    private const KEYWORDS = [
        'present perfect continuous' => 'present-perfect-continuous',
        'past perfect continuous' => 'past-perfect-continuous',
        'present perfect' => 'present-perfect',
        'present continuous' => 'present-continuous',
        'present simple' => 'present-simple',
        'past continuous' => 'past-continuous',
        'past perfect' => 'past-perfect',
        'past simple' => 'past-simple',
        'future perfect' => 'future-perfect',
        'future continuous' => 'future-continuous',
        'future simple' => 'future-simple',
        'conditional' => 'conditionals',
        'passive' => 'passive-voice',
        'reported speech' => 'reported-speech',
        'modal' => 'modals',
        'article' => 'articles',
        'preposition' => 'prepositions',
        'pronoun' => 'pronouns',
        'comparative' => 'comparatives',
        'superlative' => 'comparatives',
        'adjective' => 'adjectives',
        'adverb' => 'adverbs',
        'plural' => 'plurals',
        'countable' => 'countable-uncountable',
        'question' => 'questions',
        'phrasal verb' => 'phrasal-verbs',
        'vocabulary' => 'vocabulary',
        'word' => 'vocabulary',
    ];

    public function resolve(TestQuestion $question): ?string
    {
        $test = $question->test;

        if (! $test) {
            return null;
        }

        // Раздел грамматики урока — самый надёжный источник: его выбирал
        // человек, а не разбор строки.
        $category = $test->lesson?->grammarTopic?->title;

        if ($category && $topic = $this->fromText($category)) {
            return $topic;
        }

        foreach ([$test->title, $test->lesson?->title, $question->question] as $text) {
            if ($text && $topic = $this->fromText($text)) {
                return $topic;
            }
        }

        // Вопросы вида «What does 'fridge' mean?» — это словарь, какой бы
        // темы ни был сам тест.
        if (preg_match('/what does .+ mean/iu', $question->question)) {
            return 'vocabulary';
        }

        // Ключевые слова не сработали — берём название темы грамматики или
        // самого теста. Это по-прежнему данные из проекта, а не догадка;
        // «Test: » в начале только мешает.
        $fallback = $category ?: $test->title;

        if (! $fallback) {
            return null;
        }

        $slug = Str::slug(preg_replace('/^\s*test\s*:\s*/iu', '', $fallback));

        return $slug !== '' ? Str::limit($slug, 80, '') : null;
    }

    private function fromText(?string $text): ?string
    {
        if (! $text) {
            return null;
        }

        $haystack = Str::lower($text);

        foreach (self::KEYWORDS as $needle => $topic) {
            if (str_contains($haystack, $needle)) {
                return $topic;
            }
        }

        return null;
    }
}
