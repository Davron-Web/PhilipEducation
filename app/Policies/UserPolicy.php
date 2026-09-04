<?php

namespace App\Policies;

use App\Models\User;

/**
 * Доступ к чужому аккаунту.
 *
 * Публичной страницы «профиль другого ученика» на сайте нет — ни рейтинга,
 * ни ссылок на чужие профили. Поэтому правило простое: свой профиль видит
 * и правит сам пользователь, чужой — только администратор.
 */
class UserPolicy
{
    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->isAdmin();
    }

    /** Администратор не удаляет себя через пользовательский интерфейс. */
    public function delete(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }
}
