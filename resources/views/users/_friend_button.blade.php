{{-- Кнопка «В друзья» / «Удалить из друзей».
     Параметры: $user — чья кнопка, $isFriend — уже друг или нет.
     Себе кнопку не показываем, гостям тоже. --}}
@auth
  @if (Auth::id() !== $user->id)
    @if ($isFriend)
      <form action="{{ route('friends.destroy', $user) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
          <i class="fa-solid fa-user-minus me-1"></i>Удалить из друзей
        </button>
      </form>
    @else
      <form action="{{ route('friends.store', $user) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary">
          <i class="fa-solid fa-user-plus me-1"></i>В друзья
        </button>
      </form>
    @endif
  @endif
@endauth
