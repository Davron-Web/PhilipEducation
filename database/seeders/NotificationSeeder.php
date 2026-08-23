<?php

namespace Database\Seeders;

use App\Models\System\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Notification::create([
            'user_id' => 1,
            'title' => 'Welcome',
            'message' => 'Start learning now!',
        ]);
    }
}
