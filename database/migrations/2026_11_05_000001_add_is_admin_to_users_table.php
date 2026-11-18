<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Миграция: флаг администратора у пользователя
// и уникальное имя (оно используется в адресе /users/{name}/cities)
return new class extends Migration
{
    // php artisan migrate
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false); // по умолчанию обычный пользователь
            $table->unique('name');                      // два пользователя с одним именем не нужны
        });
    }

    // php artisan migrate:rollback
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropColumn('is_admin');
        });
    }
};
