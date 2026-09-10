<?php

/**
 * 20 текстов IELTS Reading для IeltsBatch3Seeder.
 *
 * Разбиты надвое: двадцать академических текстов с вопросами — это больше
 * тысячи строк, и одним файлом их было бы неудобно читать и править.
 */

return array_merge(
    require __DIR__.'/ielts_reading_part1.php',
    require __DIR__.'/ielts_reading_part2.php',
);
