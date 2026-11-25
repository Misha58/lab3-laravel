# Лабораторная работа 5.
Баширов Михаил - «Города Португалии»

---

### Маршруты ЛР5
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

$kate->friends->pluck('name') // друзья Kate
$kate->friends()->detach($ivan->id) // Kate удалила Ivan
$ivan->friends->pluck('name') // у Ivan Kate тоже пропала (обоюдно)
$kate->friends()->attach($ivan->id) // Kate снова добавила Ivan
$ivan->friends->pluck('name') // у Ivan Kate появилась сама (событие created)

App\Models\City::where('name_pt', 'Aveiro')->first()->comments->pluck('body') // отзывы города
$kate->comments->pluck('body') // отзывы Kate
$kate->feed()->get()->pluck('name') // лента Kate