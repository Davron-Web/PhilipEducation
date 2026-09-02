<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('title');
            $table->string('condition_type')->nullable()->after('points');
            $table->integer('condition_value')->nullable()->after('condition_type');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn(['code', 'condition_type', 'condition_value']);
        });
    }
};
