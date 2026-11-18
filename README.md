# Лабораторная работа 4. Многопользовательское приложение (Laravel + Breeze)

Приложение «Города Португалии» из ЛР3, теперь с пользователями.

## Что сделано

- Laravel Breeze: вход `/login`, регистрация `/register`, сброс пароля, профиль `/profile`.
  Контроллеры и маршруты Breeze (`routes/auth.php`) оставлены как есть,
  страницы переделаны на Bootstrap в стиле сайта (Breeze перезаписывает layout и app.js, их вернули).
- Миграции: `users.is_admin` (по умолчанию false), уникальное `users.name`, `cities.user_id` (ключ на users).
- Связи: `$user->cities`, `$city->user`.
- `/cities` — города всех пользователей, `/users` — список пользователей,
  `/users/{имя}/cities` — города одного пользователя (поиск по имени, а не по id).
- Права:
  - добавлять может любой вошедший;
  - изменять и мягко удалять — автор города или администратор;
  - восстанавливать и удалять навсегда — только администратор.
- Проверка прав на трёх уровнях:
  - шаблоны: `@can('modify-object', $city)`, `@can('admin')`, `@auth` (кнопок нет, если нет прав);
  - маршруты и контроллер: `middleware('auth')` и Gates `modify-object`, `admin` (в `AppServiceProvider`);
  - модель: события `creating`, `updating`, `deleting`, `restoring`, `forceDeleting` в `City::booted()`.
- Удалённые города видят администратор (везде) и автор (на своей странице), они в красной рамке.

## Запуск

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed     # 3 пользователя: mikhail, kate, ivan (пароль password) и 5 городов
php artisan storage:link
php artisan serve
```

Если база из ЛР3 уже есть и её не хочется пересоздавать, достаточно `php artisan migrate`
(добавятся новые поля), а владельцев назначить через tinker (ниже).

## Команды tinker

```bash
php artisan tinker
```

```php
// сделать пользователя администратором
App\Models\User::where('name', 'mikhail')->update(['is_admin' => true]);

// присвоить пользователю старые города (например, с id 1 и 2)
App\Models\City::whereIn('id', [1, 2])->update(['user_id' => 1]);

// проверить связь
App\Models\User::find(1)->cities->pluck('name');
```
