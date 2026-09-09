<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Отмечает почту всех оставшихся пользователей подтверждённой.
     *
     * Подтверждение почты отключается: письма с локальной машины уходят в
     * спам Gmail, и требование запирало живых людей на странице ввода кода.
     * Те, кто успел зарегистрироваться, пока оно действовало, остались с
     * пустым email_verified_at — а подтвердить его теперь нечем, страницы
     * больше нет.
     *
     * Дату ставим created_at, а не now(): «подтверждено при регистрации»
     * ближе к правде и не искажает возможную аналитику по срокам.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('created_at')]);
    }

    /** Откат не снимает подтверждение — иначе запер бы живых пользователей. */
    public function down(): void
    {
        //
    }
};
