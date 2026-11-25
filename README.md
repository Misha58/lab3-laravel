# Лабораторная работа 5. Связи Eloquent: отзывы и друзья
Баширов Михаил · «Города Португалии» (продолжение ЛР3 и ЛР4)

---

## ИНСТРУКЦИЯ ПО УСТАНОВКЕ (Windows + VS Code)

### Что нужно на компьютере
- **PHP 8.2+**, в `php.ini` включены (без `;` в начале строки):
  `extension=curl`, `extension=fileinfo`, `extension=gd`, `extension=mbstring`,
  `extension=openssl`, `extension=pdo_sqlite`, `extension=sqlite3`, `extension=zip`.
  Какой `php.ini` используется, показывает `php --ini`, проверка: `php -m`.
- **Composer**, **Node.js**, **Git**, **VS Code**.

### Установка с нуля (по одной команде, в папке проекта)
```
composer install
php -r "copy('.env.example', '.env');"
php artisan key:generate
php -r "touch('database/database.sqlite');"
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
php artisan serve
```
Сайт: **http://127.0.0.1:8000**.
Сидер создаёт пользователей `mikhail`, `kate`, `ivan` (почта `имя@example.com`, пароль `password`),
5 городов, дружбу mikhail ↔ kate и ivan ↔ kate и несколько отзывов.

Администратор назначается вручную:
```
php artisan tinker
App\Models\User::where('name', 'mikhail')->update(['is_admin' => true])
exit
```

### Если проект ЛР4 уже установлен (база с данными есть)
```
php artisan migrate        # добавит таблицы comments и friendships, старые данные останутся
npm run dev                # пересобрать стили (добавлены стили отзывов и ленты)
php artisan serve
```

### Если что-то не работает
| Ошибка | Что делать |
|---|---|
| `requires ext-fileinfo` (gd, zip, pdo_sqlite) | включить расширение в `php.ini` (см. выше), перезапустить терминал |
| `curl error 28 ... packagist` | `composer config -g repos.packagist composer https://mirrors.aliyun.com/composer/` и `composer config -g process-timeout 2000`, затем снова `composer install` (или VPN) |
| `npm warn`, `vulnerabilities` | норма. **Не запускать** `npm audit fix --force`: сломает Laravel Mix |
| картинки не показываются | `php artisan storage:link` |
| `Failed to listen on 127.0.0.1:8000` | `php artisan serve --port=8001` |
| `Vite manifest not found` | остался шаблон Breeze на Tailwind; страницы входа должны быть из `resources/views/auth` этого проекта |

---

## ЧТО СДЕЛАНО В ЛР5

### Вспомогательная сущность: отзывы о городах
- **Миграция** `2026_11_22_000001_create_comments_table.php`: таблица `comments`
  (`city_id` → cities, `user_id` → users, `body`, даты). Ключи с `cascadeOnDelete`:
  удалили город навсегда или пользователя — удаляются и отзывы.
- **Модель** `app/Models/Comment.php`: связи `city()` и `user()` (`belongsTo`),
  событие `creating` записывает автора = вошедший пользователь.
- **Связи «один ко многим»**: `$city->comments` (отзывы города, новые сверху),
  `$user->comments` (отзывы пользователя).
- **Контроллер** `CommentController`: `store` — добавить отзыв к **любому** городу (своему или чужому),
  `destroy` — удалить (автор отзыва или администратор, Gate `delete-comment`).
- **Шаблон** `resources/views/cities/_comments.blade.php`: список отзывов и форма,
  выводится на странице города `/cities/{id}`. В окне «Подробнее» — число отзывов и ссылка,
  на карточке города — значок с числом отзывов.

### Друзья: связь «многие ко многим» между пользователями
- **Миграция** `2026_11_22_000002_create_friendships_table.php`: таблица `friendships`
  (`user_id` — кто добавил, `friend_id` — кого), пара уникальна.
- **Модель** `User`: `friends()` → `belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')`,
  `isFriendWith($user)`, `feed()` — города друзей.
- **Контроллер** `FriendController`: `store` — добавить в друзья, `destroy` — удалить, `feed` — лента.
- **Кнопки** «В друзья» / «Удалить из друзей» (`users/_friend_button.blade.php`):
  на странице `/users`, на странице пользователя `/users/{имя}/cities` и в ленте.

### Расширенный уровень
- **Лента** `/feed` (пункт «Лента» в шапке): новые города друзей по дате добавления (новые сверху)
  и список друзей.
- **Подсветка отзывов друзей**: если вошедший пользователь открывает любой город,
  отзывы его друзей выделены зелёной полосой, иконкой и значком «друг»
  (в окне «Подробнее» — значок «есть от друзей»).
- **Событие «обоюдной дружбы»**: промежуточная модель `app/Models/Friendship.php` (Pivot).
  Связь `friends()` подключена через `->using(Friendship::class)`, поэтому при `attach` и `detach`
  срабатывают события модели:
  - `created` — A добавил B → автоматически создаётся связь B → A;
  - `deleted` — A удалил B → автоматически удаляется связь B → A.

### Маршруты ЛР5 (только для вошедших)
| Метод | Адрес | Имя | Действие |
|---|---|---|---|
| POST | `/cities/{city}/comments` | `comments.store` | добавить отзыв |
| DELETE | `/comments/{comment}` | `comments.destroy` | удалить отзыв |
| POST | `/users/{имя}/friend` | `friends.store` | добавить в друзья |
| DELETE | `/users/{имя}/friend` | `friends.destroy` | удалить из друзей |
| GET | `/feed` | `feed` | лента друзей |

### Проверка в tinker (`php artisan tinker`)
```php
$kate = App\Models\User::where('name', 'kate')->first();
$ivan = App\Models\User::where('name', 'ivan')->first();

$kate->friends->pluck('name')                  // друзья Kate
$kate->friends()->detach($ivan->id)            // Kate удалила Ivan
$ivan->friends->pluck('name')                  // у Ivan Kate тоже пропала (обоюдно)
$kate->friends()->attach($ivan->id)            // Kate снова добавила Ivan
$ivan->friends->pluck('name')                  // у Ivan Kate появилась сама (событие created)

App\Models\City::where('name_pt', 'Aveiro')->first()->comments->pluck('body')   // отзывы города
$kate->comments->pluck('body')                                                // отзывы Kate
$kate->feed()->get()->pluck('name')                                           // лента Kate
```

---

## Из ЛР4 (без изменений)
- Laravel Breeze: вход, регистрация, профиль; страницы на Bootstrap.
- `cities.user_id`, `users.is_admin`, связи `$user->cities`, `$city->user`.
- Права: Gates `modify-object`, `admin`; `middleware('auth')`; события модели `City`.
- Мягкое удаление: восстанавливать и удалять навсегда может только администратор.
