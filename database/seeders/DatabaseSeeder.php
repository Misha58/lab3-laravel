<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// Главный сидер: вызывает остальные
class DatabaseSeeder extends Seeder
{
    // Заполнение базы тестовыми данными
    public function run(): void
    {
        // три тестовых пользователя, у всех пароль "password"
        // (администратора назначаем вручную через tinker, см. README)
        foreach (['mikhail', 'kate', 'ivan'] as $name) {
            User::create([
                'name'     => $name,
                'email'    => "{$name}@example.com",
                'password' => 'password', // захешируется сам (cast 'hashed' в модели User)
            ]);
        }

        $this->call(CitySeeder::class);
    }
}
