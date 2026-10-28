<?php

namespace Database\Seeders;

use App\Models\City;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

// Заполняет таблицу cities пятью городами из лабораторной работы №2
class CitySeeder extends Seeder
{
    // Запускается командой php artisan db:seed (или migrate --seed)
    public function run(ImageService $images): void
    {
        foreach ($this->cities() as $data) {
            $data['image'] = $this->downloadImage($images, $data['image'], $data['name_pt'], $data['color']);
            unset($data['color']);
            City::create($data);
            $this->command->info("Добавлен город: {$data['name']}");
        }
    }

    // Скачивает фото из Википедии и кладёт его в storage/app/public/cities.
    // Если интернета нет или сайт не ответил — рисует цветную заглушку, чтобы сидер не падал.
    private function downloadImage(ImageService $images, string $url, string $title, string $color): string
    {
        // пробуем адрес из лабы 2, а если не вышло — обычный адрес картинок Википедии
        $urls = [$url, str_replace('thumb.wikimedia.org', 'upload.wikimedia.org', $url)];

        foreach ($urls as $try) {
            try {
                $response = Http::withHeaders(['User-Agent' => 'Lab3-Laravel/1.0 (student project)'])
                    ->timeout(20)
                    ->get($try);

                if ($response->successful()) {
                    return $images->storeFromString($response->body());
                }
            } catch (\Throwable $e) {
                // не получилось — пробуем следующий адрес
            }
        }

        $this->command->warn("Не удалось скачать фото для {$title}, поставлена заглушка (фото можно заменить через «Редактировать»).");

        return $images->placeholder($title, $color);
    }

    // Данные пяти городов (тексты из лабы 2; [слово|подсказка] — это всплывающая подсказка)
    private function cities(): array
    {
        return [
            [
                'name' => 'Абрантиш',
                'name_pt' => 'Abrantes',
                'district' => 'Сантарен',
                'summary' => 'Город в Португалии, центр одноимённого муниципалитета в составе округа Сантарен.',
                'description' => "Стоит на холме над рекой [Тежу|Самая длинная река Пиренейского полуострова (около 1000 км). Впадает в Атлантический океан у Лиссабона.]. Из-за удобного положения город веками был крепостью, над ним до сих пор стоит средневековый замок.\n\nВ 1807 году через Абрантиш на Лиссабон шла французская армия генерала [Жюно|Жан-Андош Жюно — командовал вторжением в Португалию. Наполеон дал ему титул герцога Абрантеш.].",
                'city_population' => 18600,
                'municipality_population' => 40700,
                'census_year' => null,
                'published_at' => '28.09.2026',
                'image' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/9/9d/Abrantes.JPG/330px-Abrantes.JPG',
                'color' => '#F4D35E',
            ],
            [
                'name' => 'Авейру',
                'name_pt' => 'Aveiro',
                'district' => 'Авейру',
                'summary' => 'Город в Португалии, центр одноимённого округа и муниципалитета, известный своими каналами и лагуной.',
                'description' => "Город называют «португальской Венецией»: через центр идут каналы, по которым катают туристов на ярких лодках [моличейру|Традиционные плоскодонные лодки с расписным носом. Раньше на них собирали водоросли в лагуне.].\n\nСтоит на берегу лагуны [Риа-де-Авейру|Мелководная лагуна длиной около 45 км, отделённая от океана песчаной косой.]. Раньше здесь добывали морскую соль.\n\nИзвестен сладостью «овуш-молеш» из яичного желтка и сахара.",
                'city_population' => 57000,
                'municipality_population' => 78455,
                'census_year' => 2011,
                'published_at' => '28.09.2026',
                'image' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/a/a1/Ilha_Dos_Puxadoiros_%2847261194681%29_%28cropped%29.jpg/960px-Ilha_Dos_Puxadoiros_%2847261194681%29_%28cropped%29.jpg',
                'color' => '#EE964B',
            ],
            [
                'name' => 'Агейда',
                'name_pt' => 'Águeda',
                'district' => 'Авейру',
                'summary' => 'Город в Португалии, центр одноимённого муниципалитета в составе округа Авейру. Находится в составе крупной городской агломерации Большое Авейру.',
                'description' => "Входит в агломерацию [Большое Авейру|Объединение соседних муниципалитетов вокруг города Авейру.].\n\nПрославился проектом [Umbrella Sky|Арт-инсталляция: летом над улицами подвешивают сотни разноцветных зонтиков. Проходит во время фестиваля AgitÁgueda.] — фотографии улиц под зонтиками разошлись по всему миру.\n\nОдин из центров велосипедной промышленности Португалии.",
                'city_population' => 14500,
                'municipality_population' => 49900,
                'census_year' => null,
                'published_at' => '29.09.2026',
                'image' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/2/2f/Tribunal_de_%C3%81gueda_%284506620026%29.jpg/960px-Tribunal_de_%C3%81gueda_%284506620026%29.jpg',
                'color' => '#F95738',
            ],
            [
                'name' => 'Агуалва-Касен',
                'name_pt' => 'Agualva-Cacém',
                'district' => 'Лиссабон',
                'summary' => 'Город и район в Португалии, входит в округ Лиссабон. Является составной частью муниципалитета Синтра.',
                'description' => "Пригород Лиссабона, входит в муниципалитет [Синтра|Муниципалитет к северо-западу от Лиссабона. Его дворцы и парки — объект Всемирного наследия ЮНЕСКО.].\n\nЧерез город проходит [линия Синтры|Пригородная железная дорога Лиссабон — Синтра.]: многие жители каждый день ездят по ней на работу в столицу.",
                'city_population' => 81845,
                'municipality_population' => null,
                'census_year' => 2001,
                'published_at' => '29.09.2026',
                'image' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/8/8c/Avenida_dos_Bons_Amigos_11.jpg/960px-Avenida_dos_Bons_Amigos_11.jpg',
                'color' => '#0D3B66',
            ],
            [
                'name' => 'Албуфейра',
                'name_pt' => 'Albufeira',
                'district' => 'Фару',
                'summary' => 'Город и морской порт на юге Португалии, центр одноимённого муниципалитета в округе Фару.',
                'description' => "Один из главных курортов региона [Алгарве|Самый южный регион Португалии, известный пляжами, скалами и тёплым климатом.]. Раньше это была рыбацкая деревня, теперь каждое лето сюда приезжает множество туристов.\n\nНазвание арабского происхождения: до XIII века городом владели [мавры|Так в Европе называли мусульман Северной Африки и Пиренейского полуострова.].",
                'city_population' => 31000,
                'municipality_population' => 44164,
                'census_year' => null,
                'published_at' => '30.09.2026',
                'image' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/a/a3/Albufeira_%28Portugal%29_%2810311600764%29.jpg/960px-Albufeira_%28Portugal%29_%2810311600764%29.jpg',
                'color' => '#14213D',
            ],
        ];
    }
}
