<?php

namespace App\Console\Commands\Words;

use App\Models\Vocabulary\Word;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchWordAudioCommand extends Command
{
    protected $signature = 'words:fetch-audio';

    protected $description = 'Download pronunciation audio for words with audio_checked = false, using the free dictionaryapi.dev API';

    public function handle(): int
    {
        $directory = public_path('assets/audio/words');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $total = 0;
        $withAudio = 0;
        $withoutAudio = 0;

        $pending = Word::where('audio_checked', false)->count();

        if ($pending === 0) {
            $this->info('Нечего обрабатывать — у всех слов audio_checked = true.');

            return self::SUCCESS;
        }

        $this->info("К обработке: {$pending} слов.");

        Word::where('audio_checked', false)
            ->orderBy('id')
            ->chunkById(20, function ($words) use (&$total, &$withAudio, &$withoutAudio) {
                foreach ($words as $word) {
                    $total++;

                    $audioUrl = $this->lookupAudioUrl($word->word);
                    $saved = $audioUrl ? $this->downloadAudio($audioUrl, $word->id) : false;

                    if ($saved) {
                        $word->audio_url = "assets/audio/words/{$word->id}.mp3";
                        $withAudio++;
                        $this->line("  [{$word->id}] {$word->word} — audio OK");
                    } else {
                        $word->audio_url = null;
                        $withoutAudio++;
                        $this->line("  [{$word->id}] {$word->word} — no audio (TTS fallback)");
                    }

                    $word->audio_checked = true;
                    $word->save();

                    usleep(300000);
                }
            });

        $this->newLine();
        $this->info("Готово. Обработано: {$total}. С озвучкой (mp3): {$withAudio}. Без озвучки, уйдут в TTS: {$withoutAudio}.");

        return self::SUCCESS;
    }

    private function lookupAudioUrl(string $word): ?string
    {
        try {
            $response = Http::timeout(5)
                ->get('https://api.dictionaryapi.dev/api/v2/entries/en/' . rawurlencode($word));

            if (!$response->successful()) {
                return null;
            }

            $entries = $response->json();

            if (!is_array($entries)) {
                return null;
            }

            foreach ($entries as $entry) {
                foreach (($entry['phonetics'] ?? []) as $phonetic) {
                    $audio = $phonetic['audio'] ?? null;
                    if (is_string($audio) && $audio !== '' && str_starts_with($audio, 'http')) {
                        return $audio;
                    }
                }
            }

            return null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function downloadAudio(string $url, int $wordId): bool
    {
        try {
            $response = Http::timeout(6)->get($url);

            if (!$response->successful() || $response->body() === '') {
                return false;
            }

            file_put_contents(public_path("assets/audio/words/{$wordId}.mp3"), $response->body());

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
