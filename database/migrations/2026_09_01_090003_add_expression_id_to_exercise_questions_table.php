<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_questions', function (Blueprint $table) {
            $table->foreignId('expression_id')->nullable()->after('exercise_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exercise_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expression_id');
        });
    }
};
