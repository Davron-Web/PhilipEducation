<?php

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\Role;
use App\Models\Vocabulary\Word;
use App\Models\Vocabulary\WordTranslation;
use Illuminate\Support\Facades\DB;

function dictStudent(): User
{
    return User::factory()->create([
        'role_id' => Role::factory()->student()->create()->id,
        'is_active' => true,
    ]);
}

function attachWord(User $user, string $text, bool $learned, ?int $lessonId = null): Word
{
    $word = Word::factory()->create(['word' => $text, 'lesson_id' => $lessonId]);
    WordTranslation::create(['word_id' => $word->id, 'language' => 'ru', 'translation' => 'перевод '.$text]);
    $user->words()->attach($word->id, ['learned' => $learned]);

    return $word;
}

it('не пускает гостя в личный словарь', function () {
    $this->get('/words/my')->assertRedirect('/login');
});

it('показывает только слова этого ученика', function () {
    $mine = dictStudent();
    $other = dictStudent();

    attachWord($mine, 'apple', false);
    attachWord($other, 'secret', false);

    $this->actingAs($mine)
        ->get('/words/my')
        ->assertOk()
        ->assertSee('apple')
        ->assertDontSee('secret');
});

it('фильтрует выученные и изучаемые', function () {
    $user = dictStudent();
    attachWord($user, 'known', true);
    attachWord($user, 'studying', false);

    $this->actingAs($user)->get('/words/my?filter=learned')
        ->assertSee('known')->assertDontSee('studying');

    $this->actingAs($user)->get('/words/my?filter=learning')
        ->assertSee('studying')->assertDontSee('known');
});

it('отделяет слова, добавленные самим учеником', function () {
    $user = dictStudent();
    $lesson = Lesson::factory()->create();

    attachWord($user, 'fromlesson', false, $lesson->id);
    attachWord($user, 'myownword', false, null);

    // Свои слова не привязаны к уроку — по этому их и отличаем.
    $this->actingAs($user)->get('/words/my?filter=own')
        ->assertSee('myownword')->assertDontSee('fromlesson');
});

it('добавленное слово попадает в личный словарь', function () {
    $user = dictStudent();

    $this->actingAs($user)->post('/words', [
        'word' => 'serendipity',
        'translation' => 'счастливая случайность',
    ])->assertRedirect();

    $this->actingAs($user)->get('/words/my')->assertSee('serendipity');
});

it('сообщает, когда словарь пуст', function () {
    $this->actingAs(dictStudent())
        ->get('/words/my')
        ->assertOk()
        ->assertSee('В словаре пока пусто');
});

it('грузит переводы одним запросом', function () {
    $user = dictStudent();

    for ($i = 0; $i < 15; $i++) {
        attachWord($user, 'word'.$i, false);
    }

    DB::flushQueryLog();
    DB::enableQueryLog();
    $this->actingAs($user)->get('/words/my')->assertOk();
    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    // Без eager loading было бы по запросу на перевод каждого слова.
    expect($count)->toBeLessThan(15);
});
