<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Миграция: таблица friendships — связь «многие ко многим» между пользователями.
// Одна строка: пользователь user_id добавил в друзья пользователя friend_id.
return new class extends Migration
{
    // Создание таблицы
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();             // кто добавил
            $table->foreignId('friend_id')->constrained('users')->cascadeOnDelete();    // кого добавил
            $table->timestamps();

            // одну и ту же пару нельзя добавить дважды (проверка на уровне БД)
            $table->unique(['user_id', 'friend_id']);
        });
    }

    // Откат миграции
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
