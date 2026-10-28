<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Проверка данных формы на стороне сервера (Validation Rules)
class CityRequest extends FormRequest
{
    // Первая перепись населения в Португалии прошла в 1864 году
    const FIRST_CENSUS = 1864;

    // Разрешаем отправлять форму всем (авторизации в лабе нет)
    public function authorize(): bool
    {
        return true;
    }

    // Перед проверкой: убираем пробелы по краям и пробелы внутри чисел ("78 455" -> "78455")
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'                    => trim((string) $this->name),
            'name_pt'                 => trim((string) $this->name_pt),
            'district'                => trim((string) $this->district),
            'city_population'         => preg_replace('/\s/u', '', (string) $this->city_population),
            'municipality_population' => preg_replace('/\s/u', '', (string) $this->municipality_population),
        ]);
    }

    // Правила проверки полей
    public function rules(): array
    {
        // при создании фото обязательно, при редактировании — нет
        $imageRule = $this->isMethod('post') ? 'required' : 'nullable';

        return [
            // название: русские буквы, пробел, дефис; с заглавной буквы
            'name' => ['required', 'string', 'min:2', 'max:100',
                'regex:/^[А-ЯЁ][а-яёА-ЯЁ]*([\s\-][А-ЯЁа-яё][а-яёА-ЯЁ]*)*$/u'],

            // название на португальском: латиница (в т.ч. á, ç, ã), пробел, дефис, апостроф; с заглавной;
            // уникальное — такой же уникальный индекс есть в БД (удалённые записи тоже учитываются)
            'name_pt' => ['required', 'string', 'min:2', 'max:100',
                "regex:/^[A-ZÀ-Ý][A-Za-zÀ-ÿ']*([\s\-][A-Za-zÀ-ÿ][A-Za-zÀ-ÿ']*)*$/u",
                Rule::unique('cities', 'name_pt')->ignore($this->route('city'))],

            // округ: русские буквы, пробел, дефис; с заглавной
            'district' => ['required', 'string', 'min:2', 'max:100',
                'regex:/^[А-ЯЁ][а-яёА-ЯЁ]*([\s\-][А-ЯЁа-яё][а-яёА-ЯЁ]*)*$/u'],

            // тексты: ограничение длины, без HTML-тегов
            'summary'     => ['required', 'string', 'min:20', 'max:300', 'not_regex:/<[^>]*>/'],
            'description' => ['required', 'string', 'min:50', 'max:5000', 'not_regex:/<[^>]*>/'],

            // население: целые числа; муниципалитет не меньше города (город входит в муниципалитет)
            'city_population'         => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'municipality_population' => ['nullable', 'integer', 'min:1', 'max:10000000', 'gte:city_population'],
            // год переписи (необязательно): от первой переписи до текущего года
            'census_year' => ['nullable', 'integer', 'min:' . self::FIRST_CENSUS, 'max:' . date('Y')],

            // дата публикации: не раньше 2000 года и не в будущем
            'published_at' => ['required', 'date', 'after_or_equal:2000-01-01', 'before_or_equal:today'],

            // фото: не меньше 300×200, чтобы после обрезки до 600×400 не было мыла
            'image' => [$imageRule, 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096',
                'dimensions:min_width=300,min_height=200'],
        ];
    }

    // Сообщения об ошибках на русском
    public function messages(): array
    {
        return [
            'required'        => 'Заполните поле «:attribute».',
            'string'          => 'Поле «:attribute» должно быть текстом.',
            'integer'         => 'В поле «:attribute» нужно целое число.',
            'max'             => 'Поле «:attribute» — не больше :max символов.',
            'min'             => 'Поле «:attribute» — не меньше :min символов.',
            'city_population.min'         => 'Население должно быть больше нуля.',
            'city_population.max'         => 'Слишком большое число: не больше 10 000 000.',
            'municipality_population.min' => 'Население должно быть больше нуля.',
            'municipality_population.max' => 'Слишком большое число: не больше 10 000 000.',
            'municipality_population.gte' => 'Население муниципалитета не может быть меньше населения города.',
            'census_year.min'  => 'Первая перепись в Португалии была в 1864 году.',
            'census_year.max'  => 'Год переписи не может быть в будущем.',
            'date'             => 'В поле «:attribute» нужна корректная дата.',
            'after_or_equal'   => 'Дата «:attribute» — не раньше 2000 года.',
            'before_or_equal'  => 'Дата «:attribute» не может быть в будущем.',
            'name.regex'       => 'Название — русскими буквами, с заглавной (например «Агуалва-Касен»).',
            'name_pt.regex'    => 'Название — латиницей, с заглавной (например «Águeda»).',
            'name_pt.unique'   => 'Такой город уже есть в базе (возможно, среди удалённых).',
            'district.regex'   => 'Округ — русскими буквами, с заглавной (например «Фару»).',
            'not_regex'        => 'В поле «:attribute» нельзя использовать HTML-теги.',
            'image'            => 'Файл должен быть картинкой.',
            'mimes'            => 'Допустимые форматы: jpg, png, webp.',
            'image.max'        => 'Фото должно весить не больше 4 МБ.',
            'dimensions'       => 'Фото слишком маленькое: нужно не меньше 300×200 пикселей.',
        ];
    }

    // Названия полей для сообщений об ошибках
    public function attributes(): array
    {
        return [
            'name'                    => 'Название',
            'name_pt'                 => 'Название на португальском',
            'district'                => 'Округ',
            'summary'                 => 'Краткое описание',
            'description'             => 'Подробное описание',
            'city_population'         => 'Население города',
            'municipality_population' => 'Население муниципалитета',
            'census_year'             => 'Год переписи',
            'published_at'            => 'Дата публикации',
            'image'                   => 'Фото',
        ];
    }
}
