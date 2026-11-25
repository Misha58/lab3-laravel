<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

// Промежуточная модель для таблицы friendships (связь «многие ко многим» между пользователями).
// Нужна, чтобы повесить на неё события «обоюдной дружбы».
class Friendship extends Pivot
{
    protected $table = 'friendships';

    // у таблицы есть свой id
    public $incrementing = true;

    // ---------- СОБЫТИЕ «ОБОЮДНОЙ ДРУЖБЫ» ----------
    // A добавил B -> автоматически появляется связь B -> A.
    // A удалил B  -> автоматически удаляется связь B -> A.

    protected static function booted(): void
    {
        // после добавления связи создаём обратную, если её ещё нет
        static::created(function (Friendship $friendship) {
            $reverseExists = static::where('user_id', $friendship->friend_id)
                ->where('friend_id', $friendship->user_id)
                ->exists();

            if (! $reverseExists) {
                // создание обратной связи снова вызовет created,
                // но там прямая связь уже есть, поэтому цикла не будет
                static::create([
                    'user_id'   => $friendship->friend_id,
                    'friend_id' => $friendship->user_id,
                ]);
            }
        });

        // после удаления связи удаляем и обратную
        // (запрос напрямую, без событий, чтобы не было цикла)
        static::deleted(function (Friendship $friendship) {
            static::where('user_id', $friendship->friend_id)
                ->where('friend_id', $friendship->user_id)
                ->delete();
        });
    }
}
