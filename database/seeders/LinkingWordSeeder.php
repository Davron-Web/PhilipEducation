<?php

namespace Database\Seeders;

use App\Models\Vocabulary\Expression;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Черновое наполнение категории «Связующие слова» (linking words) —
 * 5 заглушечных примеров на каждую из 6 функциональных подгрупп, чтобы
 * структура (модель/миграции/вьюхи) была видна и проверяема ещё до того,
 * как придёт финальный контент. Реальные слова и обороты, не выдуманные —
 * их можно смело показывать ученику уже сейчас, просто набор неполный.
 *
 * Идемпотентен по тексту выражения (как ExpressionSeeder) — безопасно
 * перезапускать.
 */
class LinkingWordSeeder extends Seeder
{
    /** Должно совпадать с ExpressionController::SUBCATEGORY_ORDER. */
    public const CATEGORY = 'Связующие слова';

    public function run(): void
    {
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
                'type' => 'linking_word',
                'example' => $row['example'],
                'category' => self::CATEGORY,
                'subcategory' => $row['subcategory'],
                'register' => $row['register'],
                'difficulty' => 2,
            ]);

            $expression->translations()->create([
                'language' => 'ru',
                'translation' => $row['ru'],
                'example' => $row['ru_example'],
            ]);

            $expression->translations()->create([
                'language' => 'tg',
                'translation' => $row['tg'],
                'example' => $row['tg_example'],
            ]);

            $existing[Str::lower($row['text'])] = true;
            $created++;
        }

        $this->command?->info("LinkingWordSeeder: +{$created} linking words, {$skipped} skipped as already existing.");
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function entries(): array
    {
        return [
            // ===== Начало и введение (intro) =====
            [
                'text' => 'Firstly', 'subcategory' => 'intro', 'register' => 'formal',
                'ru' => 'Во-первых', 'tg' => 'Аввалан',
                'example' => 'Firstly, we must consider the economic impact.',
                'ru_example' => 'Во-первых, необходимо учесть экономическое влияние.',
                'tg_example' => 'Аввалан, бояд таъсири иқтисодиро ба назар гирифт.',
            ],
            [
                'text' => 'To begin with', 'subcategory' => 'intro', 'register' => 'formal',
                'ru' => 'Начнём с того, что', 'tg' => 'Барои сар кардан',
                'example' => "To begin with, let's examine the causes of the problem.",
                'ru_example' => 'Начнём с того, что рассмотрим причины этой проблемы.',
                'tg_example' => 'Барои сар кардан, биёед сабабҳои ин мушкилотро баррасӣ кунем.',
            ],
            [
                'text' => 'In this essay, I will discuss', 'subcategory' => 'intro', 'register' => 'formal',
                'ru' => 'В этом эссе я рассмотрю', 'tg' => 'Дар ин иншо ман баррасӣ хоҳам кард',
                'example' => 'In this essay, I will discuss both sides of the argument.',
                'ru_example' => 'В этом эссе я рассмотрю обе стороны аргумента.',
                'tg_example' => 'Дар ин иншо ман ҳарду ҷониби баҳсро баррасӣ хоҳам кард.',
            ],
            [
                'text' => 'First of all', 'subcategory' => 'intro', 'register' => 'neutral',
                'ru' => 'Прежде всего', 'tg' => 'Пеш аз ҳама',
                'example' => "First of all, let's look at the data.",
                'ru_example' => 'Прежде всего, давайте посмотрим на данные.',
                'tg_example' => 'Пеш аз ҳама, биёед ба маълумот назар кунем.',
            ],
            [
                'text' => 'So, to start', 'subcategory' => 'intro', 'register' => 'informal',
                'ru' => 'Итак, для начала', 'tg' => 'Хуб, барои оғоз',
                'example' => 'So, to start, why do people move to big cities?',
                'ru_example' => 'Итак, для начала — почему люди переезжают в большие города?',
                'tg_example' => 'Хуб, барои оғоз — чаро одамон ба шаҳрҳои калон кӯч мебанданд?',
            ],

            // ===== Добавление (addition) =====
            [
                'text' => 'Furthermore', 'subcategory' => 'addition', 'register' => 'formal',
                'ru' => 'Более того', 'tg' => 'Ба ғайр аз ин',
                'example' => 'Furthermore, the study shows a clear correlation.',
                'ru_example' => 'Более того, исследование показывает чёткую взаимосвязь.',
                'tg_example' => 'Ба ғайр аз ин, тадқиқот алоқамандии равшанро нишон медиҳад.',
            ],
            [
                'text' => 'Moreover', 'subcategory' => 'addition', 'register' => 'formal',
                'ru' => 'Кроме того', 'tg' => 'Илова бар ин',
                'example' => 'Moreover, the policy has reduced unemployment.',
                'ru_example' => 'Кроме того, эта политика снизила безработицу.',
                'tg_example' => 'Илова бар ин, ин сиёсат бекориро коҳиш дод.',
            ],
            [
                'text' => 'In addition', 'subcategory' => 'addition', 'register' => 'neutral',
                'ru' => 'В дополнение', 'tg' => 'Ба замми ин',
                'example' => 'In addition, many students work part-time.',
                'ru_example' => 'В дополнение, многие студенты работают неполный день.',
                'tg_example' => 'Ба замми ин, бисёр донишҷӯён рӯзи нопурра кор мекунанд.',
            ],
            [
                'text' => 'Also', 'subcategory' => 'addition', 'register' => 'neutral',
                'ru' => 'Также', 'tg' => 'Ҳамчунин',
                'example' => 'Also, the price of housing has increased.',
                'ru_example' => 'Также цены на жильё выросли.',
                'tg_example' => 'Ҳамчунин, нархи манзил боло рафт.',
            ],
            [
                'text' => 'On top of that', 'subcategory' => 'addition', 'register' => 'informal',
                'ru' => 'Вдобавок ко всему', 'tg' => 'Илова бар ин ҳама',
                'example' => 'On top of that, the traffic gets worse every year.',
                'ru_example' => 'Вдобавок ко всему, пробки с каждым годом только хуже.',
                'tg_example' => 'Илова бар ин ҳама, тирбандӣ сол то сол бадтар мешавад.',
            ],

            // ===== Противопоставление (contrast) =====
            [
                'text' => 'However', 'subcategory' => 'contrast', 'register' => 'formal',
                'ru' => 'Однако', 'tg' => 'Аммо',
                'example' => 'However, not everyone agrees with this view.',
                'ru_example' => 'Однако не все согласны с этой точкой зрения.',
                'tg_example' => 'Аммо, на ҳама бо ин назар розӣ ҳастанд.',
            ],
            [
                'text' => 'Nevertheless', 'subcategory' => 'contrast', 'register' => 'formal',
                'ru' => 'Тем не менее', 'tg' => 'Бо вуҷуди ин',
                'example' => 'Nevertheless, the results were disappointing.',
                'ru_example' => 'Тем не менее, результаты разочаровали.',
                'tg_example' => 'Бо вуҷуди ин, натиҷаҳо ноумедкунанда буданд.',
            ],
            [
                'text' => 'On the other hand', 'subcategory' => 'contrast', 'register' => 'neutral',
                'ru' => 'С другой стороны', 'tg' => 'Аз ҷониби дигар',
                'example' => 'On the other hand, remote work can reduce productivity.',
                'ru_example' => 'С другой стороны, удалённая работа может снижать продуктивность.',
                'tg_example' => 'Аз ҷониби дигар, кори дурдаст метавонад маҳсулнокиро паст кунад.',
            ],
            [
                'text' => 'Although', 'subcategory' => 'contrast', 'register' => 'neutral',
                'ru' => 'Хотя', 'tg' => 'Гарчанде ки',
                'example' => 'Although the plan is expensive, it is necessary.',
                'ru_example' => 'Хотя план дорогостоящий, он необходим.',
                'tg_example' => 'Гарчанде ки нақша хеле пурхарҷ аст, он зарур аст.',
            ],
            [
                'text' => 'But', 'subcategory' => 'contrast', 'register' => 'informal',
                'ru' => 'Но', 'tg' => 'Вале',
                'example' => "It's a good idea, but it will cost too much.",
                'ru_example' => 'Это хорошая идея, но она обойдётся слишком дорого.',
                'tg_example' => 'Ин фикри хуб аст, вале хеле гарон меафтад.',
            ],

            // ===== Примеры (examples) =====
            [
                'text' => 'For instance', 'subcategory' => 'examples', 'register' => 'formal',
                'ru' => 'Например', 'tg' => 'Барои мисол',
                'example' => 'For instance, Japan has one of the highest literacy rates.',
                'ru_example' => 'Например, в Японии один из самых высоких показателей грамотности.',
                'tg_example' => 'Барои мисол, Ҷопон яке аз баландтарин сатҳи саводнокиро дорад.',
            ],
            [
                'text' => 'For example', 'subcategory' => 'examples', 'register' => 'neutral',
                'ru' => 'Например', 'tg' => 'Масалан',
                'example' => 'For example, many cities have banned plastic bags.',
                'ru_example' => 'Например, во многих городах запретили пластиковые пакеты.',
                'tg_example' => 'Масалан, дар бисёр шаҳрҳо халтаҳои пластикӣ манъ карда шудаанд.',
            ],
            [
                'text' => 'Such as', 'subcategory' => 'examples', 'register' => 'neutral',
                'ru' => 'Такие как', 'tg' => 'Ба монанди',
                'example' => 'Countries such as Sweden invest heavily in renewable energy.',
                'ru_example' => 'Такие страны, как Швеция, активно инвестируют в возобновляемую энергию.',
                'tg_example' => 'Кишварҳое ба монанди Шветсия ба энергияи барқароршаванда сармояи зиёд мегузоранд.',
            ],
            [
                'text' => 'To illustrate', 'subcategory' => 'examples', 'register' => 'formal',
                'ru' => 'Чтобы проиллюстрировать', 'tg' => 'Барои тасвир кардан',
                'example' => 'To illustrate, consider the case of small businesses during the pandemic.',
                'ru_example' => 'Чтобы проиллюстрировать, рассмотрим положение малого бизнеса во время пандемии.',
                'tg_example' => 'Барои тасвир кардан, ҳолати бизнеси хурдро дар давраи пандемия дида мебароем.',
            ],
            [
                'text' => 'Like', 'subcategory' => 'examples', 'register' => 'informal',
                'ru' => 'Как например, типа', 'tg' => 'Мисли',
                'example' => 'Some jobs, like teaching, require a lot of patience.',
                'ru_example' => 'Некоторые профессии, как например преподавание, требуют много терпения.',
                'tg_example' => 'Баъзе касбҳо, мисли муаллимӣ, сабри зиёдро талаб мекунанд.',
            ],

            // ===== Причина и следствие (cause_effect) =====
            [
                'text' => 'Therefore', 'subcategory' => 'cause_effect', 'register' => 'formal',
                'ru' => 'Поэтому', 'tg' => 'Аз ин рӯ',
                'example' => 'Therefore, governments should invest more in education.',
                'ru_example' => 'Поэтому правительствам следует больше инвестировать в образование.',
                'tg_example' => 'Аз ин рӯ, ҳукуматҳо бояд ба маориф бештар сармоя гузоранд.',
            ],
            [
                'text' => 'As a result', 'subcategory' => 'cause_effect', 'register' => 'formal',
                'ru' => 'В результате', 'tg' => 'Дар натиҷа',
                'example' => 'As a result, air pollution levels have dropped.',
                'ru_example' => 'В результате уровень загрязнения воздуха снизился.',
                'tg_example' => 'Дар натиҷа, сатҳи ифлосшавии ҳаво паст шуд.',
            ],
            [
                'text' => 'Consequently', 'subcategory' => 'cause_effect', 'register' => 'formal',
                'ru' => 'Следовательно', 'tg' => 'Аз ин лиҳоз',
                'example' => 'Consequently, many small shops have closed down.',
                'ru_example' => 'Следовательно, многие небольшие магазины закрылись.',
                'tg_example' => 'Аз ин лиҳоз, бисёр мағозаҳои хурд баста шуданд.',
            ],
            [
                'text' => 'Because of this', 'subcategory' => 'cause_effect', 'register' => 'neutral',
                'ru' => 'Из-за этого', 'tg' => 'Аз ин сабаб',
                'example' => 'Because of this, more people are choosing to cycle to work.',
                'ru_example' => 'Из-за этого всё больше людей выбирают велосипед, чтобы добраться до работы.',
                'tg_example' => 'Аз ин сабаб, шумораи бештари одамон бо велосипед ба кор мераванд.',
            ],
            [
                'text' => 'So', 'subcategory' => 'cause_effect', 'register' => 'informal',
                'ru' => 'Поэтому, так что', 'tg' => 'Пас',
                'example' => 'It was raining, so we stayed at home.',
                'ru_example' => 'Шёл дождь, так что мы остались дома.',
                'tg_example' => 'Борон меборид, пас мо дар хона мондем.',
            ],

            // ===== Заключение (conclusion) =====
            [
                'text' => 'In conclusion', 'subcategory' => 'conclusion', 'register' => 'formal',
                'ru' => 'В заключение', 'tg' => 'Дар хулоса',
                'example' => 'In conclusion, the advantages outweigh the disadvantages.',
                'ru_example' => 'В заключение, преимущества перевешивают недостатки.',
                'tg_example' => 'Дар хулоса, бартариҳо аз камбудиҳо бештаранд.',
            ],
            [
                'text' => 'To conclude', 'subcategory' => 'conclusion', 'register' => 'formal',
                'ru' => 'Подводя итог', 'tg' => 'Барои ҷамъбаст кардан',
                'example' => 'To conclude, both approaches have merit.',
                'ru_example' => 'Подводя итог, оба подхода имеют свои достоинства.',
                'tg_example' => 'Барои ҷамъбаст кардан, ҳарду равиш арзиши худро доранд.',
            ],
            [
                'text' => 'Overall', 'subcategory' => 'conclusion', 'register' => 'neutral',
                'ru' => 'В целом', 'tg' => 'Умуман',
                'example' => 'Overall, the benefits of technology outweigh its drawbacks.',
                'ru_example' => 'В целом, преимущества технологий перевешивают их недостатки.',
                'tg_example' => 'Умуман, бартариҳои технология аз камбудиҳояш зиёдтаранд.',
            ],
            [
                'text' => 'To sum up', 'subcategory' => 'conclusion', 'register' => 'neutral',
                'ru' => 'Суммируя', 'tg' => 'Ба таври мухтасар',
                'example' => 'To sum up, education remains the key to development.',
                'ru_example' => 'Суммируя, образование остаётся ключом к развитию.',
                'tg_example' => 'Ба таври мухтасар, маориф калиди рушд боқӣ мемонад.',
            ],
            [
                'text' => 'All in all', 'subcategory' => 'conclusion', 'register' => 'informal',
                'ru' => 'В общем и целом', 'tg' => 'Дар маҷмӯъ',
                'example' => 'All in all, it was a useful experience.',
                'ru_example' => 'В общем и целом, это был полезный опыт.',
                'tg_example' => 'Дар маҷмӯъ, ин таҷрибаи муфид буд.',
            ],
        ];
    }
}
