<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Сохранение фотографий: обрезка и уменьшение под размер карточки (3:2, 600×400)
class ImageService
{
    // размер картинки под вёрстку карточки
    const WIDTH = 600;
    const HEIGHT = 400;

    // папка внутри storage/app/public
    const FOLDER = 'cities';

    // Сохраняет файл из формы и возвращает путь (например "cities/abc.jpg")
    public function store(UploadedFile $file): string
    {
        return $this->storeFromString(file_get_contents($file->getRealPath()));
    }

    // Сохраняет картинку из байтов (например, скачанную из интернета в сидере)
    public function storeFromString(string $bytes): string
    {
        $source = imagecreatefromstring($bytes);   // открываем jpg / png / webp
        $result = $this->cover($source, self::WIDTH, self::HEIGHT);

        return $this->save($result);
    }

    // Рисует простую картинку-заглушку с названием (если фото не скачалось)
    public function placeholder(string $title, string $hexColor): string
    {
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        [$r, $g, $b] = sscanf($hexColor, '#%02x%02x%02x');
        imagefill($image, 0, 0, imagecolorallocate($image, $r, $g, $b));
        $white = imagecolorallocate($image, 255, 255, 255);
        // встроенный шрифт GD не знает кириллицу, поэтому пишем латиницей
        imagestring($image, 5, 24, self::HEIGHT - 40, Str::ascii($title), $white);

        return $this->save($image);
    }

    // Удаляет картинку из storage
    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    // Сохраняет готовую картинку в jpg со случайным именем и возвращает путь
    private function save(\GdImage $image): string
    {
        $path = self::FOLDER . '/' . Str::uuid() . '.jpg';
        ob_start();
        imagejpeg($image, null, 85);
        Storage::disk('public')->put($path, ob_get_clean());

        return $path;
    }

    // Масштабирует картинку так, чтобы она закрыла рамку width×height, лишнее обрезает по центру
    // (как CSS object-fit: cover)
    private function cover(\GdImage $source, int $width, int $height): \GdImage
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        // во сколько раз уменьшить исходник
        $scale = max($width / $srcW, $height / $srcH);

        // какой кусок исходника берём (по центру)
        $cropW = (int) round($width / $scale);
        $cropH = (int) round($height / $scale);
        $cropX = (int) round(($srcW - $cropW) / 2);
        $cropY = (int) round(($srcH - $cropH) / 2);

        $result = imagecreatetruecolor($width, $height);
        imagecopyresampled($result, $source, 0, 0, $cropX, $cropY, $width, $height, $cropW, $cropH);

        return $result;
    }
}
