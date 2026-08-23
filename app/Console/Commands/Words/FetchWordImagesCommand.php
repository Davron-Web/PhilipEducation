<?php

namespace App\Console\Commands\Words;

use App\Models\Vocabulary\Word;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchWordImagesCommand extends Command
{
    protected $signature = 'words:fetch-images {--limit= : Process only the first N unique words (for testing)}';

    protected $description = 'Download an illustrative photo for words without one, using the free LoremFlickr keyword image service';

    public function handle(): int
    {
        $directory = public_path('assets/images/words');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pendingWords = Word::whereNull('image')
            ->orWhere('image', '')
            ->get(['id', 'word'])
            ->groupBy(fn (Word $w) => Str::lower($w->word));

        if ($pendingWords->isEmpty()) {
            $this->info('Нечего обрабатывать — у всех слов уже есть фото.');

            return self::SUCCESS;
        }

        if ($limit = $this->option('limit')) {
            $pendingWords = $pendingWords->take((int) $limit);
        }

        $this->info("Уникальных слов без фото: {$pendingWords->count()} (строк: ".$pendingWords->flatten()->count().').');

        $withImage = 0;
        $withoutImage = 0;

        foreach ($pendingWords as $rows) {
            $sampleWord = $rows->first()->word;
            $slug = Str::slug($sampleWord) ?: 'word-'.$rows->first()->id;
            $relativePath = "assets/images/words/{$slug}.jpg";

            $saved = $this->downloadImage($sampleWord, public_path($relativePath));

            if ($saved) {
                Word::whereIn('id', $rows->pluck('id'))->update(['image' => $relativePath]);
                $withImage += $rows->count();
                $this->line("  {$sampleWord} — фото OK ({$rows->count()} строк)");
            } else {
                $withoutImage += $rows->count();
                $this->line("  {$sampleWord} — не удалось получить фото");
            }

            usleep(250000);
        }

        $this->newLine();
        $this->info("Готово. С фото: {$withImage}. Без фото: {$withoutImage}.");

        return self::SUCCESS;
    }

    private function downloadImage(string $word, string $destination): bool
    {
        try {
            $tags = implode(',', array_map('rawurlencode', preg_split('/\s+/', trim($word))));

            $response = Http::timeout(15)->get("https://loremflickr.com/320/240/{$tags}");

            if (! $response->successful() || strlen($response->body()) < 500) {
                return false;
            }

            file_put_contents($destination, $response->body());

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
