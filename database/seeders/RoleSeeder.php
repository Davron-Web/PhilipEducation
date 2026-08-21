<?php

namespace Database\Seeders;

use App\Models\User\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'admin',
                'description' => 'Administrator with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'teacher',
                'description' => 'Teacher with content management access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'student',
                'description' => 'Student with learning access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
