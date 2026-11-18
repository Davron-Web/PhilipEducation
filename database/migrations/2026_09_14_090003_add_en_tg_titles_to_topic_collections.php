<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Переводы 37 тем, перенесённых из words.category (см. предыдущую
 * миграцию), на en/tg — мои, не сверенные с носителем языка, как и
 * таджикские переводы в разделе «Выражения» ранее. Ключ сопоставления —
 * точное совпадение title_ru: он был скопирован из category один в один
 * в предыдущей миграции, так что коллизий с чужими строками нет.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->titles() as $ru => [$en, $tg]) {
            DB::table('collections')
                ->where('type', 'topic')
                ->where('title_ru', $ru)
                ->update(['title_en' => $en, 'title_tg' => $tg]);
        }
    }

    public function down(): void
    {
        DB::table('collections')->where('type', 'topic')->update(['title_en' => null, 'title_tg' => null]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private function titles(): array
    {
        return [
            'Академическая лексика и эссе' => ['Academic Vocabulary & Essays', 'Луғати академӣ ва иншо'],
            'Бизнес и предпринимательство' => ['Business & Entrepreneurship', 'Тиҷорат ва соҳибкорӣ'],
            'Внешность' => ['Appearance', 'Намуди зоҳирӣ'],
            'Деньги и финансы' => ['Money & Finance', 'Пул ва молия'],
            'Дом, мебель и интерьер' => ['Home, Furniture & Interior', 'Хона, мебел ва дохили бино'],
            'Еда и напитки' => ['Food & Drinks', 'Хӯрок ва нӯшокӣ'],
            'Животные' => ['Animals', 'Ҳайвонот'],
            'Здоровье' => ['Health', 'Саломатӣ'],
            'Идиомы и устойчивые выражения' => ['Idioms & Fixed Expressions', 'Идиомаҳо ва ибораҳои устувор'],
            'Искусство и живопись' => ['Art & Painting', 'Санъат ва наққошӣ'],
            'Кино и телевидение' => ['Cinema & Television', 'Синамо ва телевизион'],
            'Компьютеры и интернет' => ['Computers & Internet', 'Компютер ва интернет'],
            'Космос и астрономия' => ['Space & Astronomy', 'Кайҳон ва астрономия'],
            'Кулинария: методы и посуда' => ['Cooking: Methods & Utensils', 'Пухтупаз: усулҳо ва зарфҳо'],
            'Литература и повествование' => ['Literature & Storytelling', 'Адабиёт ва ҳикоягӯӣ'],
            'Музыка' => ['Music', 'Мусиқӣ'],
            'Наука и исследования' => ['Science & Research', 'Илм ва таҳқиқот'],
            'Общение и речь' => ['Communication & Speech', 'Муошират ва нутқ'],
            'Общие глаголы и действия' => ['Common Verbs & Actions', 'Феълҳо ва амалҳои умумӣ'],
            'Одежда и аксессуары' => ['Clothes & Accessories', 'Либос ва аксессуарҳо'],
            'Официально-деловой и юридический стиль' => ['Formal, Business & Legal Style', 'Услуби расмию корӣ ва ҳуқуқӣ'],
            'Политика и государство' => ['Politics & Government', 'Сиёсат ва давлат'],
            'Преступность и безопасность' => ['Crime & Safety', 'Ҷиноят ва бехатарӣ'],
            'Природа, погода и экология' => ['Nature, Weather & Ecology', 'Табиат, ҳаво ва экология'],
            'Профессии' => ['Professions', 'Касбҳо'],
            'Работа и карьера' => ['Work & Career', 'Кор ва карьера'],
            'Разное / неопределённые записи' => ['Miscellaneous', 'Гуногун'],
            'Религия и вера' => ['Religion & Faith', 'Дин ва эътиқод'],
            'Семья и отношения' => ['Family & Relationships', 'Оила ва муносибатҳо'],
            'Спорт, хобби и творчество' => ['Sports, Hobbies & Creativity', 'Варзиш, машғулият ва эҷодиёт'],
            'Технологии и техника' => ['Technology & Devices', 'Технология ва техника'],
            'Транспорт и путешествия' => ['Transport & Travel', 'Нақлиёт ва сафар'],
            'Учёба и образование' => ['Study & Education', 'Таҳсил ва маориф'],
            'Цвета' => ['Colors', 'Рангҳо'],
            'Черты характера' => ['Character Traits', 'Хислатҳои шахсият'],
            'Числа, время и даты' => ['Numbers, Time & Dates', 'Рақамҳо, вақт ва санаҳо'],
            'Эмоции и чувства' => ['Emotions & Feelings', 'Эҳсосот ва ҳиссиёт'],
        ];
    }
};
