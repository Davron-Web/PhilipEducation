<?php

namespace Database\Seeders;

use App\Models\Dialogue\Dialogue;
use App\Models\Dialogue\DialogueLine;
use App\Models\System\Level;
use Illuminate\Database\Seeder;

/**
 * Imports the categorized example-dialogues dataset from
 * database/seeders/data/dialogues.json. Idempotent: skips a dialogue if
 * one with the same slug already exists, so re-running the seeder after
 * appending more entries to the JSON file only adds the new ones.
 */
class DialogueSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/dialogues.json');

        if (! file_exists($path)) {
            $this->command?->warn("Dialogue data file not found: {$path}");

            return;
        }

        $data = json_decode(file_get_contents($path), true);
        $levels = Level::pluck('id', 'code');

        foreach ($data['dialogues'] ?? [] as $dialogueData) {
            if (Dialogue::where('slug', $dialogueData['slug'])->exists()) {
                continue;
            }

            $dialogue = Dialogue::create([
                'slug' => $dialogueData['slug'],
                'category' => $dialogueData['category'],
                'level_id' => $levels[$dialogueData['level']] ?? null,
                'title_en' => $dialogueData['title_en'],
                'title_ru' => $dialogueData['title_ru'],
            ]);

            foreach ($dialogueData['lines'] as $index => $line) {
                DialogueLine::create([
                    'dialogue_id' => $dialogue->id,
                    'order_number' => $index + 1,
                    'speaker' => $line['speaker'],
                    'name' => $line['name'],
                    'text_en' => $line['en'],
                    'text_ru' => $line['ru'],
                ]);
            }
        }

        $this->command?->info('Seeded dialogues: '.count($data['dialogues'] ?? []));
    }
}
