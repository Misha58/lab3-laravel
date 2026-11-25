<?php

namespace App\Providers;

use App\Models\City;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    // Правила доступа (Gates). В Laravel 11+ нет AuthServiceProvider,
    // поэтому правила описываем здесь. Используются в контроллере и в шаблонах (@can).
    public function boot(): void
    {
        // изменять и удалять город может его владелец или администратор
        Gate::define('modify-object', function (User $user, City $city) {
            return $user->id === $city->user_id || $user->is_admin;
        });

        // восстанавливать и удалять навсегда может только администратор
        Gate::define('admin', function (User $user) {
            return $user->is_admin;
        });

        // удалить отзыв может его автор или администратор
        Gate::define('delete-comment', function (User $user, Comment $comment) {
            return $user->id === $comment->user_id || $user->is_admin;
        });
    }
}
