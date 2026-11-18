{{-- Кнопки для удалённого города: восстановить и удалить навсегда.
     Показываются только администратору (Gate 'admin').
     $back — куда вернуться после действия (по умолчанию текущая страница). --}}
@can('admin')
  <form action="{{ route('cities.restore', $city->id) }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $back ?? url()->current() }}">
    <button type="submit" class="btn btn-outline-success" title="Восстановить">
      <i class="fa-solid fa-rotate-left me-1"></i>Восстановить
    </button>
  </form>
  <form action="{{ route('cities.purge', $city->id) }}" method="POST"
        onsubmit="return confirm('Удалить город навсегда? Вернуть его будет нельзя.')">
    @csrf
    @method('DELETE')
    <input type="hidden" name="back" value="{{ $back ?? url()->current() }}">
    <button type="submit" class="btn btn-danger" title="Удалить навсегда"><i class="fa-solid fa-trash-can"></i></button>
  </form>
@endcan
