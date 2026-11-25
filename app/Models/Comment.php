<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

// Модель Eloquent для таблицы comments — отзыв о городе
class Comment extends Model
{
    // из формы заполняется только текст; город и автор задаются в коде
    protected $fillable = ['body'];

    // числа, чтобы сравнение с id пользователя через === работало
    protected $casts = [
        'user_id' => 'integer',
        'city_id' => 'integer',
    ];

    // ---------- СВЯЗИ ----------

    // Город, к которому написан отзыв: $comment->city
    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    // Автор отзыва: $comment->user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ---------- СОБЫТИЯ ----------

    protected static function booted(): void
    {
        // перед добавлением: автором становится вошедший пользователь
        // (из консоли — сидер, tinker — автора задают вручную)
        static::creating(function (Comment $comment) {
            if (app()->runningInConsole()) {
                return;
            }
            if (! Auth::check()) {
                abort(401, 'Чтобы оставить отзыв, нужно войти.');
            }
            $comment->user_id = Auth::id();
        });
    }
}
