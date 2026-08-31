<?php

namespace Database\Seeders;

use App\Models\Ielts\IeltsSpeakingCard;
use Illuminate\Database\Seeder;

class IeltsSpeakingCardSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->cards() as $data) {
            IeltsSpeakingCard::updateOrCreate(['title' => $data['title']], $data);
        }
    }

    private function cards(): array
    {
        return [
            [
                'title' => 'Describe a person who has influenced you',
                'topic' => 'People',
                'prompt' => 'Describe a person who has had a significant influence on your life.',
                'cue_points' => [
                    'Who this person is',
                    'How you know them',
                    'What they did that influenced you',
                    'And explain why this influence was important to you',
                ],
                'prep_seconds' => 60,
                'speak_seconds' => 120,
            ],
            [
                'title' => 'Describe a memorable trip you have taken',
                'topic' => 'Travel',
                'prompt' => 'Describe a trip you took that you remember well.',
                'cue_points' => [
                    'Where you went',
                    'Who you went with',
                    'What you did there',
                    'And explain why this trip was memorable',
                ],
                'prep_seconds' => 60,
                'speak_seconds' => 120,
            ],
            [
                'title' => 'Describe a skill you would like to learn',
                'topic' => 'Personal development',
                'prompt' => 'Describe a skill that you would like to learn in the future.',
                'cue_points' => [
                    'What the skill is',
                    'Why you want to learn it',
                    'How you would learn it',
                    'And explain how this skill would help you',
                ],
                'prep_seconds' => 60,
                'speak_seconds' => 120,
            ],
        ];
    }
}
