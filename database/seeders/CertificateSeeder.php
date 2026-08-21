<?php

namespace Database\Seeders;

use App\Models\Certificate\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        Certificate::factory()->count(5)->create();
    }
}
