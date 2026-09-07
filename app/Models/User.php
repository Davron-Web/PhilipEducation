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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static string $factory = UserFactory::class;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role_id',
        'organization_id',
        'level_id',
        'points',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'points' => 'integer',
        'is_active' => 'boolean',
        // Без каста колонка приходит строкой, и любой ->diffInDays() по ней
        // падает: даты нужны как объекты, а не как текст из базы.
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
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
    public function sendEmailVerificationNotification(): void
    {
        $code = app(EmailVerificationCodeService::class)->issue($this);

        $this->notify(new VerifyEmailWithCode($code));
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }

    /** Инициал для запасного кружка, когда аватара нет. */
    public function initial(): string
    {
        return mb_strtoupper(mb_substr($this->name ?: 'U', 0, 1));
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
