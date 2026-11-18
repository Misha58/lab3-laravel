<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Главная страница сразу ведёт на список городов
Route::redirect('/', '/cities');

// Ресурсный маршрут: 7 путей cities.* (как в ЛР3)
Route::resource('cities', CityController::class);

// После входа Breeze отправляет на dashboard — у нас это список городов
Route::get('/dashboard', fn () => redirect()->route('cities.index'))
    ->middleware('auth')->name('dashboard');

// Профиль пользователя (из Breeze), только после входа
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршруты входа, регистрации, сброса пароля (из Breeze): /login, /register, ...
require __DIR__.'/auth.php';