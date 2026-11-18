<?php

namespace App\Http\Controllers;

use App\Models\User;

// Список пользователей сайта
class UserController extends Controller
{
    // GET /users — все пользователи и сколько городов у каждого
    public function index()
    {
        $users = User::withCount([
            'cities',                                                          // обычные города
            'cities as trashed_count' => fn ($query) => $query->onlyTrashed(), // мягко удалённые
        ])->orderBy('name')->get();

        return view('users.index', compact('users'));
    }
}
