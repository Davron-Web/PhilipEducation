<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'url',
        'description',
        'duration_minutes',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Адрес для <iframe>.
     *
     * Разбираем ссылку сами, а не вставляем как есть: обычная ссылка на
     * youtube.com/watch внутри iframe не открывается (YouTube это запрещает
     * заголовком X-Frame-Options), нужен адрес вида /embed/ID. Возвращаем
     * null для всего, что не опознали, — лучше не показать плеер, чем
     * вставить в страницу произвольный чужой адрес.
     */
    public function embedUrl(): ?string
    {
        $url = trim($this->url);

        if ($url === '') {
            return null;
        }

        if ($id = $this->youtubeId($url)) {
            // rel=0 — не подсовывать чужие ролики в конце.
            return 'https://www.youtube.com/embed/'.$id.'?rel=0';
        }

        if ($id = $this->vimeoId($url)) {
            return 'https://player.vimeo.com/video/'.$id;
        }

        return null;
    }

    /** Обложка ролика — для карточки в списке. */
    public function thumbnailUrl(): ?string
    {
        return ($id = $this->youtubeId($this->url))
            ? 'https://img.youtube.com/vi/'.$id.'/hqdefault.jpg'
            : null;
    }

    private function youtubeId(string $url): ?string
    {
        // youtu.be/ID, youtube.com/watch?v=ID, /embed/ID, /shorts/ID
        $patterns = [
            '~youtu\.be/([A-Za-z0-9_-]{11})~',
            '~youtube\.com/watch\?(?:.*&)?v=([A-Za-z0-9_-]{11})~',
            '~youtube\.com/embed/([A-Za-z0-9_-]{11})~',
            '~youtube\.com/shorts/([A-Za-z0-9_-]{11})~',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    private function vimeoId(string $url): ?string
    {
        return preg_match('~vimeo\.com/(?:video/)?(\d{6,})~', $url, $m) ? $m[1] : null;
    }
}
