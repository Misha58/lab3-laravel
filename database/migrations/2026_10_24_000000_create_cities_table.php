<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Миграция: создаёт таблицу cities (города Португалии)
return new class extends Migration
{
    // Создание таблицы (php artisan migrate)
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();                                                  // номер записи
            $table->string('name', 100);                                   // название на русском
            $table->string('name_pt', 100)->unique();                      // название на португальском (уникальное — проверка в БД)
            $table->string('district', 100);                               // округ
            $table->string('summary', 300);                                // короткий текст для карточки
            $table->text('description');                                   // подробный текст для окна «Подробнее»
            $table->unsignedInteger('city_population')->nullable();        // население города
            $table->unsignedInteger('municipality_population')->nullable(); // население муниципалитета
            $table->unsignedSmallInteger('census_year')->nullable();       // год переписи (если известен)
            $table->date('published_at');                                  // дата публикации карточки на сайте
            $table->string('image')->nullable();                           // путь к фото в storage/app/public
            $table->timestamps();                                          // created_at, updated_at
            $table->softDeletes();                                         // deleted_at — «мягкое» удаление
        });
    }

    // Откат миграции (php artisan migrate:rollback)
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
