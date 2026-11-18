<?php

namespace Database\Seeders;

use App\Models\Chat\ChatMessage;
use App\Models\Dialogue\Dialogue;
use App\Models\User;
use App\Models\User\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Проигрывает реплики диалогов (database/seeders/data/dialogues.json,
 * см. DialogueSeeder) в СУЩЕСТВУЮЩИЙ общий чат — как обычные ChatMessage
 * от ботов-персонажей, с паузой 2-3 секунды между репликами, чтобы в ленте
 * (она обновляется поллингом, см. ChatController::poll()) это выглядело
 * как живая переписка.
 *
 * Без очередей/cron: обычный хостинг без воркеров и планировщика, поэтому
 * пауза — просто sleep() внутри одного длительного выполнения команды.
 * Запускать вручную: php artisan db:seed --class=DialoguePlaybackSeeder
 *
 * Идемпотентна дважды: боты создаются через firstOrCreate (без дублей),
 * а уже проигранный диалог (dialogues.played_at) при повторном запуске
 * пропускается — повторный db:seed добавит только новые диалоги.
 */
class DialoguePlaybackSeeder extends Seeder
{
    public function run(): void
    {
        $dialogues = Dialogue::with('lines')->whereNull('played_at')->orderBy('id')->get();

        if ($dialogues->isEmpty()) {
            $this->command?->info('DialoguePlaybackSeeder: нет непроигранных диалогов.');

            return;
        }

        foreach ($dialogues as $dialogue) {
            $this->command?->info("Проигрываю диалог: {$dialogue->title_ru}");

            $bots = [];
            foreach ($dialogue->lines as $line) {
                $bots[$line->speaker] ??= $this->botFor($line->name);
            }

            $lines = $dialogue->lines;

            foreach ($lines as $index => $line) {
                ChatMessage::create([
                    'user_id' => $bots[$line->speaker]->id,
                    'body' => trim($line->text_en."\n".$line->text_ru),
                ]);

                $this->command?->info("  {$line->name}: {$line->text_en}");

                if ($index < $lines->count() - 1) {
                    sleep(random_int(2, 3));
                }
            }

            $dialogue->update(['played_at' => now()]);
        }
    }

    /** Один бот на имя персонажа — переиспользуется между диалогами (напр. "Waiter"). */
    private function botFor(string $name): User
    {
        return User::firstOrCreate(
            ['name' => $name, 'is_bot' => true],
            [
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
                'role_id' => Role::where('name', 'student')->value('id'),
                'is_active' => true,
                'avatar' => User::randomAvatarFilename(),
                'points' => 0,
            ]
        );
    }
}
