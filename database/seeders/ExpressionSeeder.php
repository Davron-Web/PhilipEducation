<?php

namespace Database\Seeders;

use App\Models\System\Level;
use App\Models\Vocabulary\Expression;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the initial batch of real expressions across all four types
 * (idiom, phrasal_verb, proverb, collocation) for the new "Выражения"
 * section. Follows the same pattern as VocabularyBatch1Seeder: skip any
 * expression whose text already exists (case-insensitive), so it is safe
 * to re-run.
 */
class ExpressionSeeder extends Seeder
{
    public function run(): void
    {
        $levels = Level::pluck('id', 'code');
        $existing = Expression::pluck('text')->map(fn ($t) => Str::lower($t))->flip();

        $created = 0;
        $skipped = 0;

        foreach ($this->entries() as $row) {
            if (isset($existing[Str::lower($row['text'])])) {
                $skipped++;

                continue;
            }

            $expression = Expression::create([
                'text' => $row['text'],
                'type' => $row['type'],
                'transcription' => $row['transcription'] ?? null,
                'example' => $row['example'],
                'meaning' => $row['meaning'],
                'literal_translation' => $row['literal_translation'] ?? null,
                'difficulty' => $row['difficulty'],
                'category' => $row['category'],
                'level_id' => $levels[$row['level']] ?? null,
                'base_verb' => $row['base_verb'] ?? null,
                'particle' => $row['particle'] ?? null,
                'separable' => $row['separable'] ?? null,
            ]);

            $expression->translations()->create([
                'language' => 'ru',
                'translation' => $row['translation'],
                'example' => $row['example'],
            ]);

            $existing[Str::lower($row['text'])] = true;
            $created++;
        }

        $this->command?->info("ExpressionSeeder: +{$created} expressions, {$skipped} skipped as already existing.");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function entries(): array
    {
        $idioms = 'Идиомы';
        $phrasal = 'Фразовые глаголы';
        $proverbs = 'Пословицы';
        $collocations = 'Коллокации';

        return [
            // ===== Idioms =====
            [
                'text' => 'break the ice',
                'type' => 'idiom',
                'meaning' => 'To do or say something to relieve tension or get a conversation started in an awkward situation.',
                'literal_translation' => 'разбить лёд',
                'translation' => 'растопить лёд, снять напряжение в общении',
                'example' => 'He told a joke to break the ice at the start of the meeting.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $idioms,
            ],
            [
                'text' => 'a piece of cake',
                'type' => 'idiom',
                'meaning' => 'Something that is very easy to do.',
                'literal_translation' => 'кусок торта',
                'translation' => 'проще простого, пустяковое дело',
                'example' => 'Don\'t worry, the exam was a piece of cake.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $idioms,
            ],
            [
                'text' => 'once in a blue moon',
                'type' => 'idiom',
                'meaning' => 'Something that happens very rarely.',
                'literal_translation' => 'раз в голубую луну',
                'translation' => 'очень редко, раз в сто лет',
                'example' => 'We only see each other once in a blue moon these days.',
                'difficulty' => 4, 'level' => 'B1', 'category' => $idioms,
            ],
            [
                'text' => 'hit the sack',
                'type' => 'idiom',
                'meaning' => 'To go to bed.',
                'literal_translation' => 'ударить по мешку',
                'translation' => 'пойти спать, завалиться спать',
                'example' => 'I\'m exhausted — I\'m going to hit the sack early tonight.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $idioms,
            ],
            [
                'text' => 'under the weather',
                'type' => 'idiom',
                'meaning' => 'Feeling slightly ill.',
                'literal_translation' => 'под погодой',
                'translation' => 'плохо себя чувствовать, приболеть',
                'example' => 'She stayed home because she was feeling a bit under the weather.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $idioms,
            ],
            [
                'text' => 'costs an arm and a leg',
                'type' => 'idiom',
                'meaning' => 'Something that is very expensive.',
                'literal_translation' => 'стоит руки и ноги',
                'translation' => 'стоит целое состояние',
                'example' => 'That new phone costs an arm and a leg.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $idioms,
            ],
            [
                'text' => 'let the cat out of the bag',
                'type' => 'idiom',
                'meaning' => 'To reveal a secret by accident.',
                'literal_translation' => 'выпустить кота из мешка',
                'translation' => 'проговориться, выдать секрет',
                'example' => 'She let the cat out of the bag about the surprise party.',
                'difficulty' => 4, 'level' => 'B2', 'category' => $idioms,
            ],
            [
                'text' => 'the ball is in your court',
                'type' => 'idiom',
                'meaning' => 'It is up to you to make the next decision or move.',
                'literal_translation' => 'мяч на твоей стороне поля',
                'translation' => 'слово за тобой, решение за тобой',
                'example' => 'I\'ve made my offer — the ball is in your court now.',
                'difficulty' => 4, 'level' => 'B2', 'category' => $idioms,
            ],
            [
                'text' => 'bite the bullet',
                'type' => 'idiom',
                'meaning' => 'To force yourself to do something unpleasant or difficult.',
                'literal_translation' => 'закусить пулю',
                'translation' => 'стиснуть зубы, решиться на трудное',
                'example' => 'I hate going to the dentist, but I'."'".'ll just have to bite the bullet.',
                'difficulty' => 5, 'level' => 'B2', 'category' => $idioms,
            ],
            [
                'text' => 'see eye to eye',
                'type' => 'idiom',
                'meaning' => 'To fully agree with someone.',
                'literal_translation' => 'видеть глаз в глаз',
                'translation' => 'сходиться во взглядах',
                'example' => 'My brother and I don\'t always see eye to eye on politics.',
                'difficulty' => 4, 'level' => 'B2', 'category' => $idioms,
            ],

            // ===== Phrasal verbs =====
            [
                'text' => 'give up',
                'type' => 'phrasal_verb',
                'meaning' => 'To stop trying to do something; to quit.',
                'translation' => 'сдаваться, бросать (привычку)',
                'example' => 'Don\'t give up — you\'re almost there!',
                'difficulty' => 2, 'level' => 'A2', 'category' => $phrasal,
                'base_verb' => 'give', 'particle' => 'up', 'separable' => false,
            ],
            [
                'text' => 'look after',
                'type' => 'phrasal_verb',
                'meaning' => 'To take care of someone or something.',
                'translation' => 'заботиться о, присматривать за',
                'example' => 'Can you look after my dog while I\'m away?',
                'difficulty' => 2, 'level' => 'A2', 'category' => $phrasal,
                'base_verb' => 'look', 'particle' => 'after', 'separable' => false,
            ],
            [
                'text' => 'turn off',
                'type' => 'phrasal_verb',
                'meaning' => 'To switch off a device or light.',
                'translation' => 'выключать',
                'example' => 'Please turn off the lights when you leave.',
                'difficulty' => 1, 'level' => 'A1', 'category' => $phrasal,
                'base_verb' => 'turn', 'particle' => 'off', 'separable' => true,
            ],
            [
                'text' => 'find out',
                'type' => 'phrasal_verb',
                'meaning' => 'To discover or learn information.',
                'translation' => 'узнавать, выяснять',
                'example' => 'I need to find out what time the train leaves.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $phrasal,
                'base_verb' => 'find', 'particle' => 'out', 'separable' => true,
            ],
            [
                'text' => 'run into',
                'type' => 'phrasal_verb',
                'meaning' => 'To meet someone unexpectedly.',
                'translation' => 'случайно встретить',
                'example' => 'I ran into an old friend at the supermarket yesterday.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $phrasal,
                'base_verb' => 'run', 'particle' => 'into', 'separable' => false,
            ],
            [
                'text' => 'put off',
                'type' => 'phrasal_verb',
                'meaning' => 'To postpone or delay something.',
                'translation' => 'откладывать',
                'example' => 'We had to put off the meeting until next week.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $phrasal,
                'base_verb' => 'put', 'particle' => 'off', 'separable' => true,
            ],
            [
                'text' => 'get over',
                'type' => 'phrasal_verb',
                'meaning' => 'To recover from something difficult, such as an illness or a loss.',
                'translation' => 'оправиться от, пережить',
                'example' => 'It took her months to get over the breakup.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $phrasal,
                'base_verb' => 'get', 'particle' => 'over', 'separable' => false,
            ],
            [
                'text' => 'come across',
                'type' => 'phrasal_verb',
                'meaning' => 'To find something by chance.',
                'translation' => 'случайно натолкнуться на',
                'example' => 'I came across an old photo while cleaning the attic.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $phrasal,
                'base_verb' => 'come', 'particle' => 'across', 'separable' => false,
            ],
            [
                'text' => 'set up',
                'type' => 'phrasal_verb',
                'meaning' => 'To arrange or establish something.',
                'translation' => 'устанавливать, организовывать',
                'example' => 'They set up a new office in Berlin last year.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $phrasal,
                'base_verb' => 'set', 'particle' => 'up', 'separable' => true,
            ],
            [
                'text' => 'call off',
                'type' => 'phrasal_verb',
                'meaning' => 'To cancel a planned event.',
                'translation' => 'отменять',
                'example' => 'They called off the wedding at the last minute.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $phrasal,
                'base_verb' => 'call', 'particle' => 'off', 'separable' => true,
            ],

            // ===== Proverbs =====
            [
                'text' => 'Actions speak louder than words.',
                'type' => 'proverb',
                'meaning' => 'What people do is more important and honest than what they say.',
                'literal_translation' => 'действия говорят громче слов',
                'translation' => 'о человеке судят по делам, а не по словам',
                'example' => 'He promised to help many times, but actions speak louder than words.',
                'difficulty' => 4, 'level' => 'B1', 'category' => $proverbs,
            ],
            [
                'text' => 'Better late than never.',
                'type' => 'proverb',
                'meaning' => 'It is better to do or arrive somewhere late than not at all.',
                'literal_translation' => 'лучше поздно, чем никогда',
                'translation' => 'лучше поздно, чем никогда',
                'example' => 'She finally replied to my email — better late than never.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $proverbs,
            ],
            [
                'text' => 'Don\'t judge a book by its cover.',
                'type' => 'proverb',
                'meaning' => 'You shouldn\'t judge someone or something based only on appearance.',
                'literal_translation' => 'не суди книгу по обложке',
                'translation' => 'не суди о книге по обложке, встречают по одёжке',
                'example' => 'He looked scruffy, but don\'t judge a book by its cover — he\'s a brilliant scientist.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $proverbs,
            ],
            [
                'text' => 'Practice makes perfect.',
                'type' => 'proverb',
                'meaning' => 'The more you practise something, the better you become at it.',
                'literal_translation' => 'практика делает совершенным',
                'translation' => 'повторение — мать учения, практика приводит к совершенству',
                'example' => 'Keep playing the piano every day — practice makes perfect.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $proverbs,
            ],
            [
                'text' => 'The early bird catches the worm.',
                'type' => 'proverb',
                'meaning' => 'People who act early or get up early have the best chance of success.',
                'literal_translation' => 'ранняя пташка ловит червяка',
                'translation' => 'кто рано встаёт, тому Бог подаёт',
                'example' => 'I always arrive at sales early — the early bird catches the worm.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $proverbs,
            ],
            [
                'text' => 'When in Rome, do as the Romans do.',
                'type' => 'proverb',
                'meaning' => 'When you are visiting another place, you should follow the customs of that place.',
                'literal_translation' => 'в Риме поступай как римлянин',
                'translation' => 'в чужой монастырь со своим уставом не ходят',
                'example' => 'I tried the local food even though it was unusual — when in Rome, do as the Romans do.',
                'difficulty' => 4, 'level' => 'B1', 'category' => $proverbs,
            ],
            [
                'text' => 'Every cloud has a silver lining.',
                'type' => 'proverb',
                'meaning' => 'Even difficult or sad situations have some positive aspect.',
                'literal_translation' => 'у каждой тучи есть серебряная подкладка',
                'translation' => 'нет худа без добра',
                'example' => 'I lost my job, but I found a much better one — every cloud has a silver lining.',
                'difficulty' => 4, 'level' => 'B1', 'category' => $proverbs,
            ],
            [
                'text' => 'You can\'t have your cake and eat it too.',
                'type' => 'proverb',
                'meaning' => 'You cannot have two incompatible things or enjoy the benefits of two opposite choices.',
                'literal_translation' => 'нельзя съесть торт и оставить его',
                'translation' => 'нельзя усидеть на двух стульях',
                'example' => 'You want to save money but also travel a lot — you can\'t have your cake and eat it too.',
                'difficulty' => 5, 'level' => 'B2', 'category' => $proverbs,
            ],

            // ===== Collocations =====
            [
                'text' => 'make a decision',
                'type' => 'collocation',
                'meaning' => 'To decide something.',
                'translation' => 'принимать решение',
                'example' => 'We need to make a decision by Friday.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $collocations,
            ],
            [
                'text' => 'heavy rain',
                'type' => 'collocation',
                'meaning' => 'Rain that falls in large amounts.',
                'translation' => 'сильный дождь',
                'example' => 'The match was cancelled because of heavy rain.',
                'difficulty' => 1, 'level' => 'A1', 'category' => $collocations,
            ],
            [
                'text' => 'take a break',
                'type' => 'collocation',
                'meaning' => 'To stop working for a short rest.',
                'translation' => 'сделать перерыв',
                'example' => 'Let\'s take a break and get some coffee.',
                'difficulty' => 1, 'level' => 'A1', 'category' => $collocations,
            ],
            [
                'text' => 'do homework',
                'type' => 'collocation',
                'meaning' => 'To complete schoolwork assigned for outside class.',
                'translation' => 'делать домашнее задание',
                'example' => 'She does her homework right after school every day.',
                'difficulty' => 1, 'level' => 'A1', 'category' => $collocations,
            ],
            [
                'text' => 'strong coffee',
                'type' => 'collocation',
                'meaning' => 'Coffee that has an intense flavour or high caffeine content.',
                'translation' => 'крепкий кофе',
                'example' => 'I need a strong coffee to wake up this morning.',
                'difficulty' => 1, 'level' => 'A1', 'category' => $collocations,
            ],
            [
                'text' => 'pay attention',
                'type' => 'collocation',
                'meaning' => 'To focus on and carefully consider something.',
                'translation' => 'обращать внимание',
                'example' => 'Please pay attention during the safety briefing.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $collocations,
            ],
            [
                'text' => 'make progress',
                'type' => 'collocation',
                'meaning' => 'To improve or move forward towards a goal.',
                'translation' => 'делать успехи, прогрессировать',
                'example' => 'She\'s making great progress with her English.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $collocations,
            ],
            [
                'text' => 'catch a cold',
                'type' => 'collocation',
                'meaning' => 'To become ill with a cold.',
                'translation' => 'простудиться',
                'example' => 'I caught a cold after getting soaked in the rain.',
                'difficulty' => 2, 'level' => 'A2', 'category' => $collocations,
            ],
            [
                'text' => 'raise awareness',
                'type' => 'collocation',
                'meaning' => 'To help more people know about and understand an issue.',
                'translation' => 'повышать осведомлённость',
                'example' => 'The campaign aims to raise awareness about climate change.',
                'difficulty' => 4, 'level' => 'B1', 'category' => $collocations,
            ],
            [
                'text' => 'meet a deadline',
                'type' => 'collocation',
                'meaning' => 'To finish something by the required time.',
                'translation' => 'уложиться в срок',
                'example' => 'The team worked overtime to meet the deadline.',
                'difficulty' => 3, 'level' => 'B1', 'category' => $collocations,
            ],
        ];
    }
}
