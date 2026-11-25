{{-- Отзывы о городе: список и форма добавления.
     $city->comments — связь «один ко многим» (модель City, метод comments()),
     $friendIds — id моих друзей: их отзывы подсвечиваем. --}}
<div class="card city-details mb-5" id="comments">
  <div class="card-body p-4">
    <h2 class="h4 mt-0">Отзывы <span class="badge text-bg-secondary">{{ $city->comments->count() }}</span></h2>

    {{-- форма: оставить отзыв может любой вошедший, к своему и к чужому городу --}}
    @auth
      @unless ($city->trashed())
        <form action="{{ route('comments.store', $city) }}" method="POST" class="mb-4">
          @csrf
          <label for="body" class="form-label">Ваш отзыв</label>
          <textarea id="body" name="body" rows="3" class="form-control @error('body') is-invalid @enderror"
                    required minlength="3" maxlength="1000" placeholder="Чем запомнился город?">{{ old('body') }}</textarea>
          <div class="invalid-feedback">{{ $errors->first('body') ?: 'От 3 до 1000 символов.' }}</div>
          <button type="submit" class="btn btn-primary mt-2"><i class="fa-solid fa-paper-plane me-1"></i>Отправить</button>
        </form>
      @endunless
    @else
      <p><a href="{{ route('login') }}">Войдите</a>, чтобы оставить отзыв.</p>
    @endauth

    {{-- список отзывов, новые сверху --}}
    @forelse ($city->comments as $comment)
      @php($fromFriend = $friendIds->contains($comment->user_id))
      <div class="comment {{ $fromFriend ? 'comment--friend' : '' }}">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div class="small">
            @if ($fromFriend)
              {{-- подсветка: отзыв от друга --}}
              <i class="fa-solid fa-user-group text-success me-1" title="Ваш друг"></i>
            @endif
            <a href="{{ route('users.cities', $comment->user) }}" class="fw-semibold">{{ $comment->user->name }}</a>
            @if ($fromFriend)
              <span class="badge text-bg-success ms-1">друг</span>
            @endif
            @if (Auth::id() === $comment->user_id)
              <span class="badge text-bg-secondary ms-1">вы</span>
            @endif
            <span class="text-muted ms-1">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
          </div>
          {{-- удалить отзыв может автор или администратор --}}
          @can('delete-comment', $comment)
            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="js-delete">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Удалить отзыв"><i class="fa-solid fa-trash"></i></button>
            </form>
          @endcan
        </div>
        <p class="mb-0 mt-1">{{ $comment->body }}</p>
      </div>
    @empty
      <p class="text-muted mb-0">Отзывов пока нет.</p>
    @endforelse
  </div>
</div>
