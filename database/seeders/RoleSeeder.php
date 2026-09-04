<?php

namespace Database\Seeders;

use App\Models\User\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate, а не insert: сидер запускается повторно вместе с
        // DatabaseSeeder, и вставка плодила бы дубли ролей — а по имени роли
        // определяются права, так что вторая строка «admin» ломает проверки.
        $roles = [
            'admin' => 'Administrator with full access',
            'teacher' => 'Teacher with content management access',
            'student' => 'Student with learning access',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
