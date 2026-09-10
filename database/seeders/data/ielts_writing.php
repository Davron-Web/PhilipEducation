<?php

/**
 * 20 заданий IELTS Writing для IeltsBatch3Seeder — десять Task 1 и десять
 * Task 2.
 *
 * У Task 1 данные графика лежат в chart_data и рисуются на странице, поэтому
 * цифры подобраны так, чтобы в них был описуемый сюжет: рост, спад, перелом
 * или расхождение линий. Задание без внятной динамики ничему не учит.
 */

return [
    // ─── Task 1: описание данных ─────────────────────────────────────────
    [
        'type' => 'writing_task1',
        'title' => 'Способы поездки на работу (2000–2020)',
        'prompt' => 'The chart below shows how people in one city travelled to work in 2000, 2010 and 2020. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'bar',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['2000', '2010', '2020'],
            'series' => [
                ['name' => 'Car', 'values' => [62, 54, 41]],
                ['name' => 'Public transport', 'values' => [25, 30, 36]],
                ['name' => 'Bicycle', 'values' => [5, 9, 17]],
                ['name' => 'On foot', 'values' => [8, 7, 6]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Потребление электроэнергии по источникам',
        'prompt' => 'The chart below shows the sources of electricity generation in one country between 2005 and 2025. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'line',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['2005', '2010', '2015', '2020', '2025'],
            'series' => [
                ['name' => 'Coal', 'values' => [48, 41, 30, 19, 9]],
                ['name' => 'Gas', 'values' => [30, 33, 35, 34, 30]],
                ['name' => 'Renewables', 'values' => [6, 12, 22, 34, 48]],
                ['name' => 'Nuclear', 'values' => [16, 14, 13, 13, 13]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Посещаемость музея по месяцам',
        'prompt' => 'The chart below shows the number of visitors to a city museum in each month of one year. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'line',
        'chart_data' => [
            'unit' => 'тыс. чел.',
            'categories' => ['Jan', 'Mar', 'May', 'Jul', 'Sep', 'Nov'],
            'series' => [
                ['name' => 'Adults', 'values' => [12, 18, 27, 44, 25, 14]],
                ['name' => 'Children', 'values' => [4, 9, 15, 31, 11, 5]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Расходы домохозяйств по категориям',
        'prompt' => 'The chart below shows how an average household in one country spent its income in 1990 and in 2020. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'bar',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['1990', '2020'],
            'series' => [
                ['name' => 'Housing', 'values' => [22, 34]],
                ['name' => 'Food', 'values' => [28, 17]],
                ['name' => 'Transport', 'values' => [14, 13]],
                ['name' => 'Leisure', 'values' => [9, 16]],
                ['name' => 'Other', 'values' => [27, 20]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Уровень владения языками у выпускников',
        'prompt' => 'The chart below shows the percentage of university graduates in four countries who speak a second language. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'bar',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['Country A', 'Country B', 'Country C', 'Country D'],
            'series' => [
                ['name' => '2010', 'values' => [34, 71, 22, 58]],
                ['name' => '2023', 'values' => [52, 79, 48, 61]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Продажи книг: печатные и электронные',
        'prompt' => 'The chart below shows sales of printed books and e-books in one country between 2012 and 2024. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'line',
        'chart_data' => [
            'unit' => 'млн экз.',
            'categories' => ['2012', '2016', '2020', '2024'],
            'series' => [
                ['name' => 'Printed', 'values' => [180, 142, 128, 134]],
                ['name' => 'E-books', 'values' => [22, 68, 96, 91]],
                ['name' => 'Audiobooks', 'values' => [3, 11, 34, 62]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Время, проведённое за экраном, по возрастам',
        'prompt' => 'The chart below shows average daily screen time by age group in one country in 2015 and 2024. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'bar',
        'chart_data' => [
            'unit' => 'часов в день',
            'categories' => ['12-17', '18-29', '30-49', '50-64', '65+'],
            'series' => [
                ['name' => '2015', 'values' => [4.2, 5.1, 3.8, 2.6, 1.4]],
                ['name' => '2024', 'values' => [6.8, 7.2, 5.4, 4.1, 3.2]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Переработка отходов в трёх городах',
        'prompt' => 'The chart below shows the proportion of household waste recycled in three cities between 2010 and 2022. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'line',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['2010', '2014', '2018', '2022'],
            'series' => [
                ['name' => 'Northport', 'values' => [18, 31, 44, 57]],
                ['name' => 'Eastfield', 'values' => [42, 46, 48, 49]],
                ['name' => 'Southgate', 'values' => [11, 14, 26, 45]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Состав населения по возрасту',
        'prompt' => 'The chart below shows the age structure of the population of one country in 1980 and the projection for 2040. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'bar',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['0-14', '15-29', '30-49', '50-64', '65+'],
            'series' => [
                ['name' => '1980', 'values' => [27, 24, 24, 16, 9]],
                ['name' => '2040', 'values' => [15, 17, 23, 21, 24]],
            ],
        ],
        'min_words' => 150,
    ],
    [
        'type' => 'writing_task1',
        'title' => 'Способы покупки продуктов',
        'prompt' => 'The chart below shows where people in one country bought their groceries in 2019 and 2024. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
        'chart_type' => 'bar',
        'chart_data' => [
            'unit' => '%',
            'categories' => ['2019', '2024'],
            'series' => [
                ['name' => 'Large supermarket', 'values' => [58, 44]],
                ['name' => 'Local shop', 'values' => [21, 19]],
                ['name' => 'Online delivery', 'values' => [9, 28]],
                ['name' => 'Market', 'values' => [12, 9]],
            ],
        ],
        'min_words' => 150,
    ],

    // ─── Task 2: аргументированное эссе ──────────────────────────────────
    [
        'type' => 'writing_task2',
        'title' => 'Удалённая работа и города',
        'prompt' => 'The growth of remote work is drawing people away from large cities. Some argue this will revive smaller towns; others believe it will damage urban economies. Discuss both views and give your own opinion.',
        'topic' => 'Work',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Бесплатное высшее образование',
        'prompt' => 'Some people believe that university education should be free for all students. Others argue that those who benefit financially from a degree should pay for it. Discuss both views and give your own opinion.',
        'topic' => 'Education',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Ответственность за климат',
        'prompt' => 'Some argue that individuals should change their behaviour to address climate change, while others insist that only governments and corporations can make a real difference. Discuss both views and give your own opinion.',
        'topic' => 'Environment',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Искусственный интеллект и рабочие места',
        'prompt' => 'Artificial intelligence is expected to replace many jobs in the coming decades. To what extent do you think governments should intervene to protect employment?',
        'topic' => 'Technology',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Социальные сети и подростки',
        'prompt' => 'Some countries have proposed banning social media for children under sixteen. Do the benefits of such a ban outweigh the drawbacks?',
        'topic' => 'Society',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Финансирование искусства',
        'prompt' => 'Some people think governments should fund museums, theatres and galleries. Others believe this money would be better spent on healthcare and education. Discuss both views and give your own opinion.',
        'topic' => 'Culture',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Массовый туризм',
        'prompt' => 'Tourism brings income to many regions but also damages the places visitors come to see. To what extent should governments limit the number of tourists at popular sites?',
        'topic' => 'Travel',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Четырёхдневная рабочая неделя',
        'prompt' => 'Several countries have trialled a four-day working week. Do you think this arrangement should become standard? Give reasons for your answer.',
        'topic' => 'Work',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Обучение иностранным языкам в школе',
        'prompt' => 'With translation technology improving rapidly, some argue that learning foreign languages at school is no longer necessary. To what extent do you agree or disagree?',
        'topic' => 'Education',
        'min_words' => 250,
    ],
    [
        'type' => 'writing_task2',
        'title' => 'Личные данные и удобство',
        'prompt' => 'Online services collect large amounts of personal data in exchange for convenience. Some people accept this trade; others find it unacceptable. Discuss both views and give your own opinion.',
        'topic' => 'Technology',
        'min_words' => 250,
    ],
];
