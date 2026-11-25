<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Города, которые добавил пользователь (связь «один ко многим»)
    // $user->cities — список его городов (без удалённых),
    // $user->cities()->withTrashed() — вместе с мягко удалёнными
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // Отзывы, которые написал пользователь (связь «один ко многим»): $user->comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ---------- ДРУЗЬЯ (связь «многие ко многим» через таблицу friendships) ----------

    // Друзья пользователя: $user->friends
    // using(Friendship::class) — чтобы при attach/detach срабатывали события «обоюдной дружбы»
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->using(Friendship::class)
            ->withTimestamps();
    }

    // Добавлен ли $other в друзья
    public function isFriendWith(User $other): bool
    {
        return $this->friends()->whereKey($other->id)->exists();
    }

    // Лента: города друзей, новые сверху (по дате добавления на сайт)
    public function feed()
    {
        return City::whereIn('user_id', $this->friends()->pluck('users.id'))
            ->with('user')
            ->latest();
    }
}
