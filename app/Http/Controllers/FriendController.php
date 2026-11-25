<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

// Друзья: добавить, удалить; лента новых городов от друзей
class FriendController extends Controller
{
    // GET /feed — моя лента: города друзей по дате добавления (новые сверху) и список друзей
    public function feed(Request $request)
    {
        $me = $request->user();

        return view('friends.feed', [
            'friends' => $me->friends()->orderBy('name')->get(),
            'cities'  => $me->feed()->take(50)->get(),
        ]);
    }

    // POST /users/{user}/friend — добавить в друзья
    public function store(Request $request, User $user)
    {
        $me = $request->user();

        if ($me->id === $user->id) {
            return back()->with('status', 'Нельзя добавить в друзья самого себя.');
        }

        // syncWithoutDetaching не создаст дубль, если связь уже есть;
        // обратную связь (user -> me) создаст событие в модели Friendship
        $me->friends()->syncWithoutDetaching([$user->id]);

        return back()->with('status', "{$user->name} теперь ваш друг.");
    }

    // DELETE /users/{user}/friend — удалить из друзей (обратная связь удалится сама)
    public function destroy(Request $request, User $user)
    {
        $request->user()->friends()->detach($user->id);

        return back()->with('status', "{$user->name} больше не в друзьях.");
    }
}
