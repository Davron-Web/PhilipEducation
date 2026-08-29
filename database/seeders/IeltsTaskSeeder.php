<?php

namespace Database\Seeders;

use App\Models\Ielts\IeltsTask;
use Illuminate\Database\Seeder;

class IeltsTaskSeeder extends Seeder
{
    public function run(): void
    {
        IeltsTask::query()->delete();

        foreach ($this->tasks() as $data) {
            IeltsTask::create($data);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tasks(): array
    {
        return [
            // ===== Writing Task 1 (график/таблица, минимум 150 слов) =====
            [
                'type' => 'writing_task1',
                'title' => 'Интернет-доступ в трёх странах (2005–2020)',
                'prompt' => 'The chart below shows the percentage of households with internet access in the UK, the USA and India between 2005 and 2020. Summarise the information by selecting and reporting the main features, and make comparisons where relevant. Write at least 150 words.',
                'chart_type' => 'bar',
                'chart_data' => [
                    'unit' => '%',
                    'categories' => ['2005', '2010', '2015', '2020'],
                    'series' => [
                        ['name' => 'UK', 'values' => [45, 68, 82, 94]],
                        ['name' => 'USA', 'values' => [55, 71, 85, 92]],
                        ['name' => 'India', 'values' => [2, 7, 18, 45]],
                    ],
                ],
                'min_words' => 150,
            ],
            [
                'type' => 'writing_task1',
                'title' => 'Структура расходов домохозяйства',
                'prompt' => 'The pie chart below shows the average household expenditure by category in a country in 2023. Summarise the information by selecting and reporting the main features, and make comparisons where relevant. Write at least 150 words.',
                'chart_type' => 'pie',
                'chart_data' => [
                    'unit' => '%',
                    'segments' => [
                        ['label' => 'Housing', 'value' => 32],
                        ['label' => 'Food', 'value' => 22],
                        ['label' => 'Transport', 'value' => 14],
                        ['label' => 'Education', 'value' => 12],
                        ['label' => 'Entertainment', 'value' => 10],
                        ['label' => 'Other', 'value' => 10],
                    ],
                ],
                'min_words' => 150,
            ],
            [
                'type' => 'writing_task1',
                'title' => 'Туристический поток в трёх городах (2010–2022)',
                'prompt' => 'The graph below shows the number of international tourists (in millions) visiting three cities between 2010 and 2022. Summarise the information by selecting and reporting the main features, and make comparisons where relevant. Write at least 150 words.',
                'chart_type' => 'line',
                'chart_data' => [
                    'unit' => 'млн',
                    'categories' => ['2010', '2013', '2016', '2019', '2022'],
                    'series' => [
                        ['name' => 'Paris', 'values' => [15, 16, 17, 19, 14]],
                        ['name' => 'Bangkok', 'values' => [10, 13, 17, 22, 15]],
                        ['name' => 'Dubai', 'values' => [7, 10, 13, 16, 17]],
                    ],
                ],
                'min_words' => 150,
            ],

            // ===== Writing Task 2 (эссе-рассуждение, минимум 250 слов) =====
            [
                'type' => 'writing_task2',
                'title' => 'Технологии усложняют или упрощают жизнь?',
                'prompt' => 'Some people believe that technology has made our lives more complicated, while others think it has made life easier. Discuss both these views and give your own opinion. Write at least 250 words.',
                'topic' => 'Technology',
                'min_words' => 250,
            ],
            [
                'type' => 'writing_task2',
                'title' => 'Плюсы и минусы удалённой работы',
                'prompt' => 'In many countries, more and more people are choosing to work from home instead of going to an office. What are the advantages and disadvantages of this trend? Write at least 250 words.',
                'topic' => 'Work',
                'min_words' => 250,
            ],
            [
                'type' => 'writing_task2',
                'title' => 'Транспортные заторы в мегаполисах',
                'prompt' => 'Traffic congestion is becoming a serious problem in many major cities around the world. What are the causes of this problem, and what solutions can be suggested? Write at least 250 words.',
                'topic' => 'Environment',
                'min_words' => 250,
            ],
        ];
    }
}
