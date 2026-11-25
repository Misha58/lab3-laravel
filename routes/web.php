<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Главная страница сразу ведёт на список городов
Route::redirect('/', '/cities');

// После входа Breeze отправляет на dashboard — у нас это список городов
Route::get('/dashboard', fn () => redirect()->route('cities.index'))
    ->middleware('auth')->name('dashboard');

// ---------- ТОЛЬКО ДЛЯ ВОШЕДШИХ ПОЛЬЗОВАТЕЛЕЙ (middleware auth) ----------
// Гость при попытке открыть эти адреса попадёт на страницу входа /login
Route::middleware('auth')->group(function () {
    // GET    /cities/create       cities.create   -> create
    // POST   /cities              cities.store    -> store
    // GET    /cities/{city}/edit  cities.edit     -> edit
    // PUT    /cities/{city}       cities.update   -> update
    // DELETE /cities/{city}       cities.destroy  -> destroy  (мягкое удаление)
    Route::resource('cities', CityController::class)->except(['index', 'show']);

    // действия администратора с удалёнными городами (по id, т.к. модель удалена)
    Route::post('/cities/{id}/restore', [CityController::class, 'restore'])
        ->whereNumber('id')->name('cities.restore');
    Route::delete('/cities/{id}/purge', [CityController::class, 'purge'])
        ->whereNumber('id')->name('cities.purge');

    // отзывы: добавить к любому городу, удалить (автор отзыва или админ)
    Route::post('/cities/{city}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // друзья: добавить / удалить (по имени пользователя) и лента новых городов от друзей
    Route::post('/users/{user:name}/friend', [FriendController::class, 'store'])->name('friends.store');
    Route::delete('/users/{user:name}/friend', [FriendController::class, 'destroy'])->name('friends.destroy');
    Route::get('/feed', [FriendController::class, 'feed'])->name('feed');

    // профиль пользователя (из Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- ДОСТУПНО ВСЕМ ----------
// GET /cities         cities.index -> index
// GET /cities/{city}  cities.show  -> show  (withTrashed — чтобы владелец и админ могли открыть удалённый)
Route::resource('cities', CityController::class)->only(['index', 'show'])->withTrashed(['show']);

// список пользователей
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// города одного пользователя; {user:name} — ищем пользователя по имени, а не по id
Route::get('/users/{user:name}/cities', [CityController::class, 'index'])->name('users.cities');

// маршруты входа, регистрации, сброса пароля (из Breeze): /login, /register, ...
require __DIR__.'/auth.php';
