# Лабораторная работа №3. Laravel QuickStart
Баширов Михаил · «Города Португалии»

---

## ИНСТРУКЦИЯ: КАК ЗАПУСТИТЬ С НУЛЯ (Windows + VS Code)

### Шаг 1. Установить программы (один раз)
1. **PHP 8.3 или новее**: скачать zip с https://windows.php.net/download (версия *Thread Safe x64*), распаковать в `C:\php`.
   Добавить `C:\php` в переменную среды **Path** (Пуск → «Изменение системных переменных среды» → Переменные среды → Path → Создать).
2. **Настроить php.ini**. В папке `C:\php` скопировать `php.ini-development` и назвать копию `php.ini`.
   Windows скрывает расширение, поэтому файл выглядит как «php» со значком шестерёнки.
   Открыть его и **убрать `;` в начале** этих строк:
   ```
   extension_dir = "ext"
   extension=curl
   extension=fileinfo
   extension=gd
   extension=mbstring
   extension=openssl
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```
3. **Composer**: https://getcomposer.org/download (Composer-Setup.exe).
4. **Node.js** (LTS): https://nodejs.org
5. **Git**: https://git-scm.com
6. **VS Code** и в нём расширение **Laravel** (для подсветки кода).

Проверка: закрыть и заново открыть терминал, затем выполнить
```
php -v          # версия 8.3+
php -m          # в списке есть fileinfo, gd, pdo_sqlite, sqlite3, zip
composer -V
node -v
git --version
```

### Шаг 2. Положить проект в правильную папку
Распаковать архив, например, в **`C:\lab3`**.
Не стоит класть проект в OneDrive или в путь с русскими буквами и пробелами: из-за этого всё тормозит и ломается `storage:link`.
В VS Code: File → Open Folder → `C:\lab3`. Терминал: Terminal → New Terminal.

### Шаг 3. Установить и запустить (по одной команде)
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
Открыть в браузере **http://127.0.0.1:8000**.

Что делает каждая команда:
| Команда | Что делает |
|---|---|
| `composer install` | скачивает Laravel и PHP-библиотеки (папка `vendor`) |
| `copy .env.example .env` | создаёт файл настроек |
| `key:generate` | создаёт ключ шифрования приложения |
| `touch database.sqlite` | создаёт пустой файл базы данных SQLite |
| `migrate --seed` | создаёт таблицу `cities` и добавляет 5 городов (фото скачиваются из Википедии) |
| `storage:link` | делает ссылку `public/storage` → `storage/app/public`, чтобы картинки открывались в браузере |
| `npm install` | скачивает Bootstrap, jQuery, FontAwesome, Laravel Mix (папка `node_modules`) |
| `npm run dev` | собирает SCSS и JS в `public/css/main.css` и `public/js/app.js` |
| `php artisan serve` | запускает сайт |

### Если что-то пошло не так
| Ошибка | Что делать |
|---|---|
| `requires ext-fileinfo` (или gd, zip, pdo_sqlite) | не включено расширение в `C:\php\php.ini` (шаг 1.2), потом перезапустить терминал |
| `curl error 28 ... repo.packagist.org` / `exceeded the timeout` | packagist медленно открывается. Выполнить:<br>`composer config -g repos.packagist composer https://mirrors.aliyun.com/composer/`<br>`composer config -g process-timeout 2000`<br>и снова `composer install` (или включить VPN) |
| при `npm install` много `warn`, `vulnerabilities` | это нормально. **Не запускать** `npm audit fix --force`: он обновит webpack и сломает Laravel Mix |
| `Не удалось скачать фото …, поставлена заглушка` | не открылась Википедия; фото можно загрузить через «Редактировать» |
| картинки не показываются | ещё раз `php artisan storage:link` |
| `Failed to listen on 127.0.0.1:8000` | порт занят: `php artisan serve --port=8001` |

### Как запускать в следующий раз
Достаточно `php artisan serve`. Остановить сервер: **Ctrl+C**.
Если меняешь стили или JS, во втором терминале запусти `npm run watch`: он пересобирает файлы при каждом сохранении.

---

## ЧТО СДЕЛАНО (где искать по пунктам задания)

| Требование | Где |
|---|---|
| Bootstrap, jQuery и свои стили собираются через Laravel Mix | `webpack.mix.js`, `resources/scss/`, `resources/js/app.js` → результат в `public/css`, `public/js` |
| Таблица создаётся миграцией, поля в snake_case | `database/migrations/2026_10_24_000000_create_cities_table.php` |
| Eloquent-модель, мутаторы | `app/Models/City.php` |
| Контроллер: index, create, store, show, edit, update, destroy | `app/Http/Controllers/CityController.php` |
| Маршруты по правилам именования (`cities.index` и т.д.) | `routes/web.php` (`Route::resource`) |
| Окно «Подробнее» — отдельное blade-представление и действие show | `resources/views/cities/_details.blade.php`, `show.blade.php`, `_info.blade.php` |
| Форма добавления/редактирования (одна и та же) | `resources/views/cities/_form.blade.php`, `create.blade.php`, `edit.blade.php` |
| Валидация в форме (HTML5 + Bootstrap, прямо при вводе) | атрибуты `required`, `pattern`, `min`, `max` в `_form.blade.php` + `resources/js/app.js` |
| Валидация в контроллере (Validation Rules) | `app/Http/Requests/CityRequest.php` |
| Загрузка фото с обрезкой под вёрстку (600×400), хранение в storage | `app/Services/ImageService.php` → `storage/app/public/cities` |
| Soft Deletes | `$table->softDeletes()` в миграции, `use SoftDeletes` в модели, `delete()` в `destroy` |
| 5 объектов из лабы 2 | `database/seeders/CitySeeder.php` |

**Мутаторы** (`app/Models/City.php`):
- `published_at`: дату в любом виде («24.10.2026» или «2026-10-24») сохраняет как `Y-m-d`, а при чтении отдаёт объект Carbon;
- `name`, `name_pt`, `district`: убирают пробелы по краям;
- `city_population`, `municipality_population`: «78 455» сохраняется как число 78455;
- вычисляемые поля: `population_text` (текст про население), `description_html` (абзацы и подсказки), `image_url`.

**Подсказки (Popover)** из лабы 2 сохранены. В подробном описании слово с подсказкой пишется так: `[Тежу|Самая длинная река полуострова]`.

**Проверки полей** (и в браузере, и на сервере):
- название и округ: русские буквы, с заглавной; название на португальском: латиница, с заглавной, **не повторяется** (уникальный индекс в БД);
- население: только цифры, муниципалитет не меньше города; год переписи — от 1864 (первая перепись в Португалии) до текущего;
- дата публикации: с 2000 года и не в будущем;
- тексты: ограничение длины, HTML-теги запрещены;
- фото: jpg/png/webp, до 4 МБ, не меньше 300×200.

### Проверка в tinker (`php artisan tinker`)
```php
$c = App\Models\City::create(['name' => 'Лиссабон', 'name_pt' => 'Lisboa', 'district' => 'Лиссабон', 'summary' => 'Столица и крупнейший город Португалии.', 'description' => 'Лиссабон стоит на семи холмах на берегу реки [Тежу|Самая длинная река полуострова].', 'city_population' => '545 923', 'published_at' => '24.10.2026'])
$c->getAttributes()['published_at']    // "2026-10-24" — сработал мутатор даты
$c->city_population                    // 545923 — пробелы убраны
$c->population_text                    // "Население — 545 923 (город)."
$c->delete()                           // мягкое удаление
App\Models\City::onlyTrashed()->get()  // запись осталась в БД с deleted_at
App\Models\City::onlyTrashed()->restore()  // вернуть удалённые
```

---

## GIT: коммиты, push, pull, архив для сдачи

```
git config --global user.name "Баширов Михаил"
git config --global user.email "почта@от.github"
git init

git add .gitignore .gitattributes .editorconfig .env.example artisan composer.json composer.lock app/Models/User.php app/Providers bootstrap config public/index.php public/.htaccess public/robots.txt public/favicon.ico routes/console.php storage tests phpunit.xml database/migrations/0001_01_01_000000_create_users_table.php database/migrations/0001_01_01_000001_create_cache_table.php database/migrations/0001_01_01_000002_create_jobs_table.php database/factories database/.gitignore app/Http/Controllers/Controller.php
git commit -m "Установлен Laravel 12"

git add package.json package-lock.json webpack.mix.js resources/scss resources/js public/css public/js public/fonts public/mix-manifest.json
git commit -m "Laravel Mix: Bootstrap, jQuery и стили из лабы 2"

git add database/migrations/2026_10_24_000000_create_cities_table.php app/Models/City.php app/Services database/seeders
git commit -m "Миграция, модель City с мутаторами, сидер"

git add app/Http routes/web.php resources/views
git commit -m "Контроллер, маршруты, blade-шаблоны, CRUD, валидация"

git add .
git commit -m "README"
```
Если какой-то коммит пишет `nothing to commit`, просто переходи к следующему.

**Push.** На github.com: New repository → имя `lab3-laravel` → без README → Create. Потом:
```
git branch -M main
git remote add origin https://github.com/ЛОГИН/lab3-laravel.git
git push -u origin main
```

**Pull.** На GitHub открыть `README.md` → карандаш → дописать строку → Commit changes. Потом в терминале:
```
git pull
```

**Архив для сдачи** (без `vendor` и `node_modules`):
```
git archive -o archive.zip HEAD
```
