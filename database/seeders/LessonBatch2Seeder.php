<?php

namespace Database\Seeders;

/**
 * Ещё 50 уроков — по десять на каждый уровень от A1 до C1.
 *
 * Наследуется от BulkLessonSeeder, а не копирует его: сборка урока из темы
 * (текст, слова, три упражнения, тест) уже написана и проверена, и второй
 * её экземпляр разошёлся бы с первым при первой же правке. Здесь только
 * сами темы.
 *
 * Темы лежат в database/seeders/data/ по файлу на уровень: десять тем с
 * примерами, словарём и тестом — это несколько сотен строк, и все пятьдесят
 * в одном классе превратились бы в файл, который невозможно читать.
 *
 * Идемпотентен по паре «уровень + название»: повторный прогон не создаёт
 * дублей и не трогает уже существующие уроки.
 *
 * Темы подобраны так, чтобы не пересекаться со 101 уроком, которые уже
 * есть: это пробелы в программе, а не второй заход по тому же материалу.
 */
class LessonBatch2Seeder extends BulkLessonSeeder
{
    /** @return array<int, array<string, mixed>> */
    protected function topics(): array
    {
        return array_merge(
            require __DIR__.'/data/lessons_a1.php',
            require __DIR__.'/data/lessons_a2.php',
            require __DIR__.'/data/lessons_b1.php',
            require __DIR__.'/data/lessons_b2.php',
            require __DIR__.'/data/lessons_c1.php',
        );
    }
}
