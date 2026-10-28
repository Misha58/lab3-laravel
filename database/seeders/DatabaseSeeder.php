<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Главный сидер: вызывает остальные
class DatabaseSeeder extends Seeder
{
    // Заполнение базы тестовыми данными
    public function run(): void
    {
        $this->call(CitySeeder::class);
    }
}
