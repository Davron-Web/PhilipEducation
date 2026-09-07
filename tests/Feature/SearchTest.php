<?php

use App\Models\Book\Book;
use App\Models\Content\GrammarTopic;
use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\Role;
use App\Models\Vocabulary\Word;
use App\Models\Vocabulary\WordTranslation;
use App\Services\SearchService;

function searcher(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

it('не пускает гостя в поиск', function () {
    $this->get('/search?q=present')->assertRedirect('/login');

    // Приложение рендерит JSON-ошибки только для api/*, поэтому и запрос
    // подсказок гость получает редиректом, а не 401.
    $this->getJson('/search/suggest?q=present')->assertRedirect('/login');
});

it('находит урок по названию', function () {
    Lesson::factory()->create(['title' => 'Present Perfect Basics', 'is_published' => true]);

    $this->actingAs(searcher())
        ->get('/search?q=perfect')
        ->assertOk()
        ->assertSee('Present Perfect Basics');
});

it('не показывает неопубликованные уроки', function () {
    Lesson::factory()->create(['title' => 'Черновик про Perfect', 'is_published' => false]);

    $this->actingAs(searcher())
        ->get('/search?q=Perfect')
        ->assertOk()
        ->assertDontSee('Черновик про Perfect');
});

it('находит слово по русскому переводу', function () {
    $word = Word::factory()->create(['word' => 'apple']);
    WordTranslation::create(['word_id' => $word->id, 'language' => 'ru', 'translation' => 'яблоко']);

    // Ученик одинаково часто ищет и по-английски, и по-русски.
    $groups = app(SearchService::class)->search('яблоко');

    expect($groups->has('words'))->toBeTrue()
        ->and($groups['words']->first()['title'])->toBe('apple');
});

it('ставит совпадение в названии выше совпадения в описании', function () {
    Lesson::factory()->create(['title' => 'Про запятые', 'description' => 'Здесь встречается слово perfect', 'is_published' => true, 'order_number' => 1]);
    Lesson::factory()->create(['title' => 'Perfect Tenses', 'description' => 'Ни при чём', 'is_published' => true, 'order_number' => 99]);

    $titles = app(SearchService::class)->search('perfect')['lessons']->pluck('title')->all();

    expect($titles[0])->toBe('Perfect Tenses');
});

it('игнорирует запрос короче двух символов', function () {
    Lesson::factory()->count(3)->create(['is_published' => true]);

    // По одному символу нашлась бы половина сайта — это шум, а не результат.
    expect(app(SearchService::class)->search('a')->count())->toBe(0);
});

it('не считает знак процента подстановочным', function () {
    Lesson::factory()->count(3)->create(['is_published' => true]);

    // Без экранирования LIKE запрос «%» вернул бы вообще всё.
    expect(app(SearchService::class)->search('%%')->count())->toBe(0);
});

it('отдаёт подсказки сгруппированными', function () {
    Lesson::factory()->create(['title' => 'Present Simple', 'is_published' => true]);
    GrammarTopic::factory()->create(['title' => 'Present Continuous']);
    Book::factory()->create(['title' => 'Presents for Everyone']);

    $response = $this->actingAs(searcher())->getJson('/search/suggest?q=present');

    $response->assertOk()->assertJsonStructure(['query', 'total', 'groups']);

    expect(array_keys($response->json('groups')))
        ->toContain('lessons', 'grammar', 'books');
});

it('ограничивает число подсказок в каждой группе', function () {
    Lesson::factory()->count(12)->create(['title' => 'Present Lesson', 'is_published' => true]);

    $groups = app(SearchService::class)->search('present', SearchService::SUGGEST_LIMIT);

    expect($groups['lessons']->count())->toBe(SearchService::SUGGEST_LIMIT);
});

it('сообщает, когда ничего не нашлось', function () {
    $this->actingAs(searcher())
        ->get('/search?q=щщщщщ')
        ->assertOk()
        ->assertSee('Ничего не нашлось');
});
