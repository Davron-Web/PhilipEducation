<?php

namespace App\Services;

use App\Models\Book\Book;
use App\Models\Content\GrammarTopic;
use App\Models\Content\Lesson;
use App\Models\Test\Test;
use App\Models\Vocabulary\Expression;
use App\Models\Vocabulary\Word;
use Illuminate\Support\Collection;

/**
 * Поиск по учебным материалам.
 *
 * Обычный LIKE, а не полнотекстовый индекс: материала около трёх тысяч
 * записей, и на таком объёме LIKE отвечает за миллисекунды, а MATCH
 * потребовал бы отдельных индексов и по-разному ведёт себя в MySQL и
 * SQLite (на котором идут тесты). Когда материала станет в разы больше —
 * менять надо будет только этот класс.
 */
class SearchService
{
    /** Сколько результатов показывать в подсказках под строкой поиска. */
    public const SUGGEST_LIMIT = 5;

    /**
     * @return Collection<string, Collection<int, array{title: string, subtitle: ?string, url: string}>>
     */
    public function search(string $query, int $perGroup = 10): Collection
    {
        $query = trim($query);

        // Один символ даёт полтаблицы в ответ — это не результат, а шум.
        if (mb_strlen($query) < 2) {
            return collect();
        }

        $groups = collect([
            'lessons' => $this->lessons($query, $perGroup),
            'grammar' => $this->grammar($query, $perGroup),
            'words' => $this->words($query, $perGroup),
            'expressions' => $this->expressions($query, $perGroup),
            'books' => $this->books($query, $perGroup),
            'tests' => $this->tests($query, $perGroup),
        ]);

        return $groups->reject(fn (Collection $items) => $items->isEmpty());
    }

    /** Сколько всего нашлось — для заголовка страницы результатов. */
    public function countAll(Collection $groups): int
    {
        return $groups->sum(fn (Collection $items) => $items->count());
    }

    private function lessons(string $q, int $limit): Collection
    {
        return Lesson::query()
            ->with('level')
            ->where('is_published', true)
            ->where(fn ($sub) => $sub
                ->where('title', 'like', $this->like($q))
                ->orWhere('description', 'like', $this->like($q)))
            // Совпадение в названии важнее совпадения в описании: иначе урок,
            // где слово упомянуто вскользь, оказывался выше профильного.
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', [$this->like($q)])
            ->orderBy('order_number')
            ->limit($limit)
            ->get()
            ->map(fn (Lesson $lesson) => [
                'title' => $lesson->title,
                'subtitle' => $lesson->level?->code,
                'url' => route('lessons.show', $lesson->id),
            ]);
    }

    private function grammar(string $q, int $limit): Collection
    {
        return GrammarTopic::query()
            ->with('level')
            ->where(fn ($sub) => $sub
                ->where('title', 'like', $this->like($q))
                ->orWhere('category', 'like', $this->like($q)))
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', [$this->like($q)])
            ->limit($limit)
            ->get()
            ->map(fn (GrammarTopic $topic) => [
                'title' => $topic->title,
                'subtitle' => $topic->category ?: $topic->level?->code,
                'url' => route('grammartopics.show', $topic->id),
            ]);
    }

    private function words(string $q, int $limit): Collection
    {
        return Word::query()
            ->with('translations')
            // Ищем и по английскому слову, и по переводу: ученик одинаково
            // часто вводит и «apple», и «яблоко».
            ->where(fn ($sub) => $sub
                ->where('word', 'like', $this->like($q))
                ->orWhereHas('translations', fn ($t) => $t->where('translation', 'like', $this->like($q))))
            ->orderByRaw('CASE WHEN word LIKE ? THEN 0 ELSE 1 END', [$q.'%'])
            ->limit($limit)
            ->get()
            ->map(fn (Word $word) => [
                'title' => $word->word,
                'subtitle' => $word->translations->first()?->translation,
                'url' => route('words.show', $word->id),
            ]);
    }

    private function expressions(string $q, int $limit): Collection
    {
        return Expression::query()
            ->with('translations')
            ->where(fn ($sub) => $sub
                ->where('text', 'like', $this->like($q))
                ->orWhere('meaning', 'like', $this->like($q))
                ->orWhereHas('translations', fn ($t) => $t->where('translation', 'like', $this->like($q))))
            ->orderByRaw('CASE WHEN text LIKE ? THEN 0 ELSE 1 END', [$this->like($q)])
            ->limit($limit)
            ->get()
            ->map(fn (Expression $expression) => [
                'title' => $expression->text,
                'subtitle' => $expression->translations->first()?->translation ?? $expression->meaning,
                'url' => route('expressions.show', $expression->id),
            ]);
    }

    private function books(string $q, int $limit): Collection
    {
        return Book::query()
            ->where(fn ($sub) => $sub
                ->where('title', 'like', $this->like($q))
                ->orWhere('author', 'like', $this->like($q)))
            ->limit($limit)
            ->get()
            ->map(fn (Book $book) => [
                'title' => $book->title,
                'subtitle' => $book->author,
                // У книг нет страницы-карточки, только читалка.
                'url' => route('books.read', $book->id),
            ]);
    }

    private function tests(string $q, int $limit): Collection
    {
        return Test::query()
            ->where('is_published', true)
            ->where('title', 'like', $this->like($q))
            ->limit($limit)
            ->get()
            ->map(fn (Test $test) => [
                'title' => $test->title,
                'subtitle' => null,
                'url' => route('tests.show', $test->id),
            ]);
    }

    /**
     * Экранируем спецсимволы LIKE.
     *
     * Без этого «%» в запросе означал бы «что угодно», и поиск по одному
     * проценту вернул бы вообще всё.
     */
    private function like(string $q): string
    {
        $escape = chr(92);

        // Собираем экранирование через chr(92), чтобы в исходнике не было
        // частокола обратных слэшей, в котором легко ошибиться.
        $safe = str_replace(
            [$escape, '%', '_'],
            [$escape.$escape, $escape.'%', $escape.'_'],
            $q
        );

        return '%'.$safe.'%';
    }
}
