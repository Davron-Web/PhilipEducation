<?php

namespace App\Services;

class ChatModerationService
{
    /**
     * Проверка на мат по всем языкам config/profanity.php. Текст режется
     * на "слова" по пробелам/пунктуации, внутри каждого слова схлопываются
     * не-буквенные символы (точки, дефисы, звёздочки — частый приём обхода
     * фильтров вида "с-у-к-а"), затем ищется вхождение (не точное
     * совпадение) запрещённого корня — чтобы ловить падежные окончания
     * ("сука"/"суки"/"суку") без морфологического анализа. Осознанно не
     * защищено от продвинутого литспика — см. комментарий в конфиге.
     */
    public function containsProfanity(string $text): bool
    {
        $banned = array_merge(...array_values(config('profanity')));

        foreach ($this->tokenize($text) as $token) {
            if ($token === '') {
                continue;
            }

            foreach ($banned as $word) {
                if ($word !== '' && str_contains($token, mb_strtolower($word, 'UTF-8'))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Ссылки: http(s)://, www. и голые домены вида "слово.зона"
     * (example.com, сайт.рф). Само решение "запретить всем, кроме
     * админов" принимает вызывающий код (StoreChatMessageRequest) —
     * этот метод только определяет факт наличия ссылки в тексте.
     */
    public function containsLink(string $text): bool
    {
        return (bool) preg_match(
            '/(https?:\/\/|www\.)\S+|\b[a-zа-я0-9-]+\.(com|net|org|ru|tj|tg|info|io|me|xyz|top|рф)\b/iu',
            $text
        );
    }

    /** @return array<int, string> */
    private function tokenize(string $text): array
    {
        $lower = mb_strtolower($text, 'UTF-8');

        $chunks = preg_split('/[\s,!?;:()"\'«»]+/u', $lower, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_map(
            fn (string $chunk) => (string) preg_replace('/[.\-_*]+/u', '', $chunk),
            $chunks
        );
    }
}
