<?php

namespace App\Models;

use App\Models\Book\Book;
use App\Models\Book\BookRead;
use App\Models\Certificate\Certificate;
use App\Models\Content\LessonComment;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\StudyStatistic;
use App\Models\Gamification\Title;
use App\Models\Gamification\XpEvent;
use App\Models\System\Level;
use App\Models\System\Notification;
use App\Models\User\Role;
use App\Models\User\UserProgress;
use App\Models\User\UserResult;
use App\Models\Vocabulary\Expression;
use App\Models\Vocabulary\Word;
use App\Notifications\VerifyEmailWithCode;
use App\Services\EmailVerificationCodeService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static string $factory = UserFactory::class;

    protected $fillable = [
        'name',
        'name_latin',
        'email',
        'avatar',
        'password',
        'role_id',
        'organization_id',
        'level_id',
        'points',
        'is_active',
        'chat_banned_until',
        'show_in_leaderboard',
        'is_bot',
        'lang',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'points' => 'integer',
        'is_active' => 'boolean',
        'show_in_leaderboard' => 'boolean',
        'is_bot' => 'boolean',
        // Без каста колонка приходит строкой, и любой ->diffInDays() по ней
        // падает: даты нужны как объекты, а не как текст из базы.
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'previous_login_at' => 'datetime',
        'chat_banned_until' => 'datetime',
    ];

    /**
     * Проверка роли по имени
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Проверка является ли пользователь админом
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /** Забанен ли сейчас в общем чате — дата в прошлом или null считаются "не забанен". */
    public function isChatBanned(): bool
    {
        return $this->chat_banned_until !== null && $this->chat_banned_until->isFuture();
    }

    /**
     * Готовая ссылка на аватар или null, если он не загружен.
     *
     * Шаблоны не должны знать, на каком диске лежит файл: сегодня это
     * локальный public, завтра может быть S3 — меняется только здесь.
     */
    /**
     * Отправка письма с подтверждением.
     *
     * Переопределено, чтобы код выдавался ровно там же, где отправляется
     * письмо: и при регистрации, и при повторной отправке, и при смене
     * адреса в настройках — иначе легко получить письмо с кодом, которого
     * нет в базе, или наоборот.
     */
    /**
     * Отправка кода подтверждения.
     *
     * Подтверждение почты сейчас отключено: контракт MustVerifyEmail с
     * модели снят, поэтому Laravel этот метод не зовёт, и сам по себе он
     * не вызывается ниоткуда. Механизм оставлен целиком — сервис, таблица
     * кодов, письмо — чтобы включить обратно после переезда на хостинг,
     * где почта будет настроена нормально.
     */
    public function sendEmailVerificationNotification(): void
    {
        $code = app(EmailVerificationCodeService::class)->issue($this);

        $this->notify(new VerifyEmailWithCode($code));
    }

    /**
     * URL аватара — всегда что-то реальное, никогда пустой кружок.
     *
     * users.avatar хранит одно из трёх:
     * 1) имя файла из готового набора (config('avatars.files'), выбрано в
     *    настройках профиля или назначено при регистрации/ботам) —
     *    отдаём из public/avatars напрямую;
     * 2) путь старого файла, загруженного вручную ДО того, как загрузку
     *    убрали в пользу галереи (см. историю ProfileController) — такие
     *    продолжают работать, просто выбрать новый такой же нельзя;
     * 3) null/что угодно нераспознанное (включая старые внешние ссылки,
     *    если они где-то остались) — тогда детерминированно, по id,
     *    подставляем один из готового набора: один и тот же пользователь
     *    всегда получает одну и ту же заглушку, а не случайную на каждый
     *    рендер.
     *
     * asset(), а не Storage::disk('public')->url(): последний берёт хост
     * статически из config('filesystems.disks.public.url'), вычисленного
     * один раз из APP_URL при загрузке конфига — не из хоста текущего
     * запроса. Если APP_URL не совпадает с тем, как реально открывают сайт
     * (типичная ситуация в локальной среде вроде OSPanel), получался URL
     * на чужой хост/порт. asset() берёт хост из самого запроса.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $gallery = config('avatars.files', []);

                if ($this->avatar && in_array($this->avatar, $gallery, true)) {
                    return asset('avatars/'.$this->avatar);
                }

                // Старый загруженный вручную файл — проверяем, что он
                // физически ещё на диске, иначе это будет битая картинка.
                if ($this->avatar
                    && ! str_starts_with($this->avatar, 'http://')
                    && ! str_starts_with($this->avatar, 'https://')
                    && Storage::disk('public')->exists($this->avatar)) {
                    return asset('storage/'.$this->avatar);
                }

                if (empty($gallery)) {
                    return null;
                }

                return asset('avatars/'.$gallery[$this->id % count($gallery)]);
            },
        );
    }

    /** Инициал для запасного кружка — используется, только если набор аватаров вообще пуст. */
    public function initial(): string
    {
        return mb_strtoupper(mb_substr($this->name ?: 'U', 0, 1));
    }

    /**
     * Случайное имя файла из готового набора — для регистрации нового
     * пользователя и для ботов (UserFactory::bot()), чтобы у них сразу
     * было что-то осмысленное в users.avatar, а не только вычисленный на
     * лету запасной вариант в avatarUrl().
     */
    public static function randomAvatarFilename(): ?string
    {
        $gallery = config('avatars.files', []);

        return $gallery ? $gallery[array_rand($gallery)] : null;
    }

    /**
     * Сколько уроков реально завершено — единая точка правды: раньше этот
     * же запрос был отдельно продублирован в DashboardController::index()
     * и ProfileController::statsFor(), теперь оба зовут этот метод.
     */
    public function completedLessonsCount(): int
    {
        return $this->progress()->where('is_completed', true)->count();
    }

    /**
     * Число дней подряд (включая сегодня, либо вчера — если сегодня ученик
     * ещё не успел позаниматься), в которые был завершён хотя бы один урок.
     * Перенесено из DashboardController::currentStreak() без изменения
     * логики — нужно было в двух местах (дашборд и достижения), поэтому
     * теперь это метод модели, а не приватный метод одного контроллера.
     */
    public function currentStreak(): int
    {
        $completedDates = $this->progress()
            ->whereNotNull('completed_at')
            ->pluck('completed_at')
            ->map(fn ($date) => $date->toDateString())
            ->unique();

        if ($completedDates->isEmpty()) {
            return 0;
        }

        $cursor = now()->toDateString();
        if (! $completedDates->contains($cursor)) {
            $cursor = now()->subDay()->toDateString();
        }

        $streak = 0;
        while ($completedDates->contains($cursor)) {
            $streak++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        return $streak;
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Сотрудник платформы, а не ученик.
     *
     * Отдельный метод, чтобы права «видеть чужой прогресс» можно было выдать
     * преподавателю, не раздавая ему доступ к админке: там проверка идёт по
     * конкретной роли через RoleMiddleware.
     */
    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isTeacher();
    }

    /**
     * Организация ученика. NULL — частное лицо, и это нормальный случай:
     * платформа работает и без организаций.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(UserResult::class);
    }

    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'user_words')
            ->withPivot('learned', 'correct_answers', 'wrong_answers', 'last_reviewed_at')
            ->withTimestamps();
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function expressions(): BelongsToMany
    {
        return $this->belongsToMany(Expression::class, 'user_expressions')
            ->withPivot('learned', 'correct_answers', 'wrong_answers', 'last_reviewed_at')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function xpEvents(): HasMany
    {
        return $this->hasMany(XpEvent::class);
    }

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class, 'user_titles')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    /**
     * Текущее звание — самое старшее из полученных.
     *
     * Титулы не отбираются: пройденный порог остаётся в истории, а показывать
     * имеет смысл только верхний.
     */
    public function currentTitle(): ?Title
    {
        return $this->titles()->orderByDesc('min_xp')->first();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function studyStatistic(): HasMany
    {
        return $this->hasMany(StudyStatistic::class);
    }

    public function bookReads(): HasMany
    {
        return $this->hasMany(BookRead::class);
    }

    public function ieltsSubmissions(): HasMany
    {
        return $this->hasMany(\App\Models\Ielts\IeltsSubmission::class);
    }

    public function ieltsPassageAttempts(): HasMany
    {
        return $this->hasMany(\App\Models\Ielts\IeltsPassageAttempt::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_reads')
            ->withPivot('current_page', 'completed_at')
            ->withTimestamps();
    }

    /** Попытки прохождения тестов — подробности с разбором ответов. */
    public function testAttempts(): HasMany
    {
        return $this->hasMany(\App\Models\Test\TestAttempt::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(\App\Models\Billing\Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Billing\Payment::class);
    }

    /**
     * Действующая подписка (включая отменённую, но ещё не истёкшую —
     * оплаченный период дорабатывает до конца).
     */
    public function activeSubscription(): ?\App\Models\Billing\Subscription
    {
        return $this->subscriptions()
            ->whereIn('status', [
                \App\Models\Billing\Subscription::STATUS_ACTIVE,
                \App\Models\Billing\Subscription::STATUS_CANCELLED,
            ])
            // ends_at = null — бессрочный доступ, выданный админом.
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->orderByRaw('ends_at IS NULL DESC')
            ->latest('ends_at')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Доступен ли материал этого уровня. Уровни из payment.free_levels
     * открыты всем; всё выше — по подписке. Материал без уровня считаем
     * бесплатным: закрывать то, что не удалось классифицировать, хуже,
     * чем показать лишнее.
     */
    public function canAccessLevel(?string $levelCode): bool
    {
        if ($levelCode === null || in_array($levelCode, config('payment.free_levels', []), true)) {
            return true;
        }

        return $this->hasActiveSubscription();
    }

    /** Админам платные разделы открыты без подписки. */
    public function canAccessPaidSection(string $section): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return ! in_array($section, config('payment.paid_sections', []), true)
            || $this->hasActiveSubscription();
    }
}
