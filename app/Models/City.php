<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Модель Eloquent для таблицы cities
class City extends Model
{
    use SoftDeletes; // delete() не стирает запись, а заполняет deleted_at

    // поля, которые можно заполнять из формы (create / update)
    protected $fillable = [
        'name', 'name_pt', 'district', 'summary', 'description',
        'city_population', 'municipality_population', 'census_year',
        'published_at', 'image',
    ];

    // user_id приводим к числу, чтобы сравнение с $user->id через === работало
    protected $casts = [
        'user_id' => 'integer',
    ];

    // ---------- СВЯЗЬ С ПОЛЬЗОВАТЕЛЕМ ----------

    // Владелец города: $city->user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Отзывы о городе (связь «один ко многим»), новые сверху: $city->comments
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // ---------- ПРОВЕРКА ПРАВ НА УРОВНЕ МОДЕЛИ (события Eloquent) ----------
    // Эти функции срабатывают прямо перед SQL-запросом, поэтому защищают данные
    // из любого места программы, даже если в контроллере проверку забыли.

    protected static function booted(): void
    {
        // перед добавлением: нужен вход, владельцем становится текущий пользователь
        static::creating(function (City $city) {
            if (self::isConsole()) {
                return;
            }
            self::requireLogin();
            $city->user_id = Auth::id();
        });

        // перед изменением: только владелец или администратор
        static::updating(function (City $city) {
            if (self::isConsole()) {
                return;
            }
            self::requireOwnerOrAdmin($city);
        });

        // перед удалением (мягким и полным): только владелец или администратор
        static::deleting(function (City $city) {
            if (self::isConsole()) {
                return;
            }
            self::requireOwnerOrAdmin($city);
        });

        // перед восстановлением: только администратор
        static::restoring(function (City $city) {
            if (self::isConsole()) {
                return;
            }
            self::requireAdmin();
        });

        // перед удалением без возврата: только администратор
        static::forceDeleting(function (City $city) {
            if (self::isConsole()) {
                return;
            }
            self::requireAdmin();
        });
    }

    // Сидеры и tinker работают из консоли без входа на сайт, им проверки не нужны
    private static function isConsole(): bool
    {
        return app()->runningInConsole();
    }

    // Нет входа — ошибка 401
    private static function requireLogin(): void
    {
        if (! Auth::check()) {
            abort(401, 'Нужно войти на сайт.');
        }
    }

    // Чужой город и не администратор — ошибка 403
    private static function requireOwnerOrAdmin(City $city): void
    {
        self::requireLogin();
        if (Auth::id() !== $city->user_id && ! Auth::user()->is_admin) {
            abort(403, 'Это не ваш город.');
        }
    }

    // Не администратор — ошибка 403
    private static function requireAdmin(): void
    {
        self::requireLogin();
        if (! Auth::user()->is_admin) {
            abort(403, 'Действие доступно только администратору.');
        }
    }

    // ---------- МУТАТОР ДЛЯ ДАТЫ ----------

    // Дата публикации:
    // set — принимает "24.10.2026" или "2026-10-24" и сохраняет в БД как Y-m-d
    // get — строку из БД превращает в объект Carbon (можно вызвать ->format('d.m.Y'))
    protected function publishedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value) : null,
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    // ---------- МУТАТОРЫ ДЛЯ ТЕКСТА ----------

    // Название: убираем лишние пробелы по краям
    protected function name(): Attribute
    {
        return Attribute::make(set: fn ($value) => trim($value));
    }

    // Название на португальском: тоже убираем пробелы
    protected function namePt(): Attribute
    {
        return Attribute::make(set: fn ($value) => trim($value));
    }

    // Округ: убираем пробелы
    protected function district(): Attribute
    {
        return Attribute::make(set: fn ($value) => trim($value));
    }

    // ---------- МУТАТОРЫ ДЛЯ ЧИСЕЛ ----------
    // Население можно ввести с пробелами ("78 455") — сохраняем как число 78455

    protected function cityPopulation(): Attribute
    {
        return Attribute::make(set: fn ($value) => $this->toNumber($value));
    }

    protected function municipalityPopulation(): Attribute
    {
        return Attribute::make(set: fn ($value) => $this->toNumber($value));
    }

    // Убирает из строки всё, кроме цифр; пустое значение -> null
    private function toNumber($value): ?int
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        return $digits === '' ? null : (int) $digits;
    }

    // ---------- ВЫЧИСЛЯЕМЫЕ ПОЛЯ (только чтение) ----------

    // Текст про население для карточки: "Население — 57 000 (город), 78 455 (муниципалитет), 2011 год."
    protected function populationText(): Attribute
    {
        return Attribute::make(get: function () {
            $parts = [];
            if ($this->city_population) {
                $parts[] = number_format($this->city_population, 0, '', ' ') . ' (город)';
            }
            if ($this->municipality_population) {
                $parts[] = number_format($this->municipality_population, 0, '', ' ') . ' (муниципалитет)';
            }
            if (! $parts) {
                return 'Данных о населении нет.';
            }
            $year = $this->census_year ? ", {$this->census_year} год" : '';

            return 'Население — ' . implode(', ', $parts) . $year . '.';
        });
    }

    // Подробный текст для окна: абзацы и подсказки.
    // В тексте подсказка пишется так: [Тежу|Самая длинная река полуострова]
    // и превращается в слово с всплывающей подсказкой Popover.
    protected function descriptionHtml(): Attribute
    {
        return Attribute::make(get: function () {
            $html = '';
            // абзацы разделены пустой строкой
            foreach (preg_split('/\R\s*\R/', trim($this->description)) as $paragraph) {
                $text = e($paragraph); // экранируем, чтобы нельзя было вставить свой HTML
                $text = preg_replace(
                    '/\[([^\[\]|]+)\|([^\[\]]+)\]/u',
                    '<span class="hint" tabindex="0" data-bs-content="$2">$1</span>',
                    $text
                );
                $html .= "<p>{$text}</p>";
            }

            return $html;
        });
    }

    // Ссылка на картинку для тега <img>
    protected function imageUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->image ? Storage::url($this->image) : null);
    }
}
