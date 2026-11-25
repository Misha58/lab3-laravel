<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Миграция: таблица comments — отзывы пользователей о городах (вспомогательная сущность)
return new class extends Migration
{
    // Создание таблицы
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // к какому городу отзыв; удалили город навсегда — удаляются и его отзывы
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            // кто написал отзыв; удалили пользователя — удаляются и его отзывы
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');      // текст отзыва
            $table->timestamps();      // created_at — дата отзыва
        });
    }

    // Откат миграции
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
