<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * users.last_login_at перезаписывается на now() при каждом входе (см.
     * RecordLastLogin), поэтому к моменту рендера дашборда в НЕМ уже лежит
     * время текущего визита — «сколько дней ученик не заходил» посчитать
     * не из чего. previous_login_at хранит значение last_login_at ЗА ШАГ
     * до перезаписи — то, что реально нужно для приветствия Phil.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('previous_login_at')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('previous_login_at');
        });
    }
};
