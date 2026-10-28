<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Services\ImageService;
use Illuminate\Http\Request;

// Контроллер ресурса «города»: все CRUD-действия
class CityController extends Controller
{
    // Сервис для сохранения и обрезки картинок подставляет сам Laravel
    public function __construct(private ImageService $images)
    {
    }

    // GET /cities — список всех городов (по алфавиту)
    public function index()
    {
        $cities = City::orderBy('name')->get();

        return view('cities.index', compact('cities'));
    }

    // GET /cities/create — пустая форма добавления
    public function create()
    {
        // сразу подставляем сегодняшнюю дату публикации
        return view('cities.create', ['city' => new City(['published_at' => now()])]);
    }

    // POST /cities — сохранение нового города
    public function store(CityRequest $request)
    {
        $data = $request->validated();                                   // только проверенные поля
        $data['image'] = $this->images->store($request->file('image')); // обрезаем и сохраняем фото

        $city = City::create($data);

        return redirect()->route('cities.index')
            ->with('status', "Город «{$city->name}» добавлен.");
    }

    // GET /cities/{city} — подробная информация
    // по AJAX отдаём только содержимое модального окна, иначе — целую страницу
    public function show(Request $request, City $city)
    {
        $view = $request->ajax() ? 'cities._details' : 'cities.show';

        return view($view, compact('city'));
    }

    // GET /cities/{city}/edit — форма редактирования (та же, что и для добавления)
    public function edit(City $city)
    {
        return view('cities.edit', compact('city'));
    }

    // PUT /cities/{city} — сохранение изменений
    public function update(CityRequest $request, City $city)
    {
        $data = $request->validated();

        // если выбрали новое фото — сохраняем его и удаляем старое
        if ($request->hasFile('image')) {
            $this->images->delete($city->image);
            $data['image'] = $this->images->store($request->file('image'));
        } else {
            unset($data['image']); // фото не меняем
        }

        $city->update($data);

        return redirect()->route('cities.index')
            ->with('status', "Город «{$city->name}» изменён.");
    }

    // DELETE /cities/{city} — мягкое удаление (Soft Delete)
    // запись остаётся в БД с заполненным deleted_at, фото не удаляем
    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')
            ->with('status', "Город «{$city->name}» удалён.");
    }
}
