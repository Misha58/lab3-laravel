<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Миграция: у каждого города появляется владелец (ключ user_id -> users.id)
return new class extends Migration
{
    // php artisan migrate
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // nullable — у старых городов из ЛР3 владельца пока нет, его назначим через tinker;
            // если пользователя удалят, его города останутся, а user_id станет null
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    // php artisan migrate:rollback
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
