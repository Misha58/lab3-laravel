<?php

use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

// Главная страница сразу ведёт на список городов
Route::redirect('/', '/cities');

// Ресурсный маршрут создаёт 7 путей по правилам именования Laravel:
// GET    /cities              cities.index    -> index
// GET    /cities/create       cities.create   -> create
// POST   /cities              cities.store    -> store
// GET    /cities/{city}       cities.show     -> show
// GET    /cities/{city}/edit  cities.edit     -> edit
// PUT    /cities/{city}       cities.update   -> update
// DELETE /cities/{city}       cities.destroy  -> destroy
Route::resource('cities', CityController::class);
