# Лабораторная работа 4

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
