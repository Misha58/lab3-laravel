<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

// Контроллер ресурса «города»: все CRUD-действия + восстановление и полное удаление
class CityController extends Controller
{
    // Сервис для сохранения и обрезки картинок подставляет сам Laravel
    public function __construct(private ImageService $images)
    {
    }

    // GET /cities              — города всех пользователей
    // GET /users/{name}/cities — города одного пользователя
    // Удалённые города видит администратор (везде) и сам владелец (на своей странице)
    public function index(?User $user = null)
    {
        $me = Auth::user();

        if (is_null($user)) {
            $query = City::query();
            $withTrashed = $me && $me->is_admin;
        } else {
            $query = $user->cities();
            $withTrashed = $me && ($me->is_admin || $me->id === $user->id);
        }

        if ($withTrashed) {
            $query->withTrashed();
        }

        $cities = $query->with('user')->withCount('comments')->orderBy('name')->get();

        return view('cities.index', [
            'cities'   => $cities,
            'owner'    => $user, // null — значит смотрим всех
            // дружу ли я с владельцем страницы — для кнопки «В друзья»
            'isFriend' => $me && $user && $me->isFriendWith($user),
        ]);
    }

    // GET /cities/create — пустая форма добавления (только после входа, см. routes/web.php)
    public function create()
    {
        // сразу подставляем сегодняшнюю дату публикации
        return view('cities.create', ['city' => new City(['published_at' => now()])]);
    }

    // POST /cities — сохранение нового города
    // user_id записывается в модели (событие creating)
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
        // удалённый город могут открыть только владелец и администратор
        if ($city->trashed() && ! $this->canSeeTrashed($city)) {
            abort(404);
        }

        $view = $request->ajax() ? 'cities._details' : 'cities.show';

        // отзывы вместе с авторами; id моих друзей — для подсветки их отзывов
        $city->load('comments.user');
        $friendIds = Auth::user()?->friends()->pluck('users.id') ?? collect();

        return view($view, compact('city', 'friendIds'));
    }

    // GET /cities/{city}/edit — форма редактирования (та же, что и для добавления)
    public function edit(City $city)
    {
        $this->checkOwner($city);

        return view('cities.edit', compact('city'));
    }

    // PUT /cities/{city} — сохранение изменений
    public function update(CityRequest $request, City $city)
    {
        // проверяем права до сохранения фото, чтобы чужой файл не попал на сервер
        $this->checkOwner($city);

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
    public function destroy(Request $request, City $city)
    {
        $this->checkOwner($city);

        $city->delete();

        return redirect($this->backUrl($request))
            ->with('status', "Город «{$city->name}» удалён.");
    }

    // POST /cities/{id}/restore — восстановить удалённый город (только администратор)
    // удалённая модель не найдётся через City $city, поэтому принимаем id
    public function restore(Request $request, int $id)
    {
        Gate::authorize('admin'); // иначе ошибка 403

        $city = City::onlyTrashed()->findOrFail($id);
        $city->restore();

        return redirect($this->backUrl($request))
            ->with('status', "Город «{$city->name}» восстановлен.");
    }

    // DELETE /cities/{id}/purge — удалить город навсегда вместе с фото (только администратор)
    public function purge(Request $request, int $id)
    {
        Gate::authorize('admin');

        $city = City::onlyTrashed()->findOrFail($id);
        $city->forceDelete();
        $this->images->delete($city->image);

        return redirect($this->backUrl($request))
            ->with('status', "Город «{$city->name}» удалён навсегда.");
    }

    // ---------- ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ----------

    // Проверка через Gate: изменять город может владелец или администратор
    private function checkOwner(City $city): void
    {
        if (Gate::denies('modify-object', $city)) {
            abort(403, 'Изменять этот город может только его автор или администратор.');
        }
    }

    // Может ли текущий пользователь видеть удалённый город
    private function canSeeTrashed(City $city): bool
    {
        $me = Auth::user();

        return $me && ($me->is_admin || $me->id === $city->user_id);
    }

    // Куда вернуться после действия: адрес из скрытого поля back (только наш сайт),
    // иначе — общий список городов
    private function backUrl(Request $request): string
    {
        $back = (string) $request->input('back');

        return str_starts_with($back, url('/')) ? $back : route('cities.index');
    }
}
