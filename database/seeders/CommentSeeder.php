<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

// Тестовые друзья и отзывы (ЛР5)
class CommentSeeder extends Seeder
{
    // Запускается из DatabaseSeeder после пользователей и городов
    public function run(): void
    {
        $users = User::pluck('id', 'name'); // ['mikhail' => 1, 'kate' => 2, ...]
        if ($users->count() < 3) {
            return;
        }

        // дружба: обратная связь создастся сама (событие в модели Friendship)
        User::find($users['mikhail'])->friends()->attach($users['kate']);
        User::find($users['ivan'])->friends()->attach($users['kate']);
        $this->command->info('Друзья: mikhail ↔ kate, ivan ↔ kate');

        // отзывы: [кто, к какому городу (name_pt), текст]
        $comments = [
            ['kate',    'Aveiro',    'Катались на лодке по каналам, очень красиво, особенно на закате.'],
            ['ivan',    'Aveiro',    'Обязательно попробуйте овуш-молеш, сладко, но вкусно.'],
            ['mikhail', 'Águeda',    'Зонтики над улицами видели в июле, фото получились отличные.'],
            ['kate',    'Albufeira', 'Пляжи хорошие, но летом очень много туристов.'],
            ['ivan',    'Abrantes',  'Из замка открывается красивый вид на реку Тежу.'],
        ];

        foreach ($comments as [$name, $namePt, $text]) {
            $city = City::where('name_pt', $namePt)->first();
            if (! $city) {
                continue;
            }
            $comment = new Comment(['body' => $text]);
            // город и автора задаём вручную: в консоли нет вошедшего пользователя
            $comment->city_id = $city->id;
            $comment->user_id = $users[$name];
            $comment->save();
        }
        $this->command->info('Добавлено отзывов: ' . count($comments));
    }
}
