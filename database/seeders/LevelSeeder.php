<?php

namespace Database\Seeders;

use App\Models\System\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        Level::insert([
            [
                'code' => 'A1',
                'name' => 'Beginner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'A2',
                'name' => 'Elementary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'B1',
                'name' => 'Intermediate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'B2',
                'name' => 'Upper-Intermediate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C1',
                'name' => 'Advanced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C2',
                'name' => 'Proficiency',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
