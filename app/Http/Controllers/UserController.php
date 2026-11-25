<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Список пользователей сайта
class UserController extends Controller
{
    // GET /users — все пользователи и сколько городов у каждого
    public function index()
    {
        $users = User::withCount([
            'cities',                                                          // обычные города
            'cities as trashed_count' => fn ($query) => $query->onlyTrashed(), // мягко удалённые
            'friends',                                                         // сколько друзей
        ])->orderBy('name')->get();

        // id моих друзей — чтобы показать кнопку «Добавить» или «Удалить из друзей»
        $friendIds = Auth::user()?->friends()->pluck('users.id') ?? collect();

        return view('users.index', compact('users', 'friendIds'));
    }
}
