{{-- Содержимое модального окна «Подробнее» (отдаётся действием show по AJAX) --}}
<div class="modal-header">
  <h2 class="modal-title h5">{{ $city->name }}</h2>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
</div>
<div class="modal-body">
  <div class="row g-3">
    <div class="col-md-5">
      <img src="{{ $city->image_url }}" class="img-fluid rounded w-100 modal-photo" alt="Вид на город {{ $city->name }}">
    </div>
    <div class="col-md-7">
      @include('cities._info')
    </div>
  </div>
  {{-- отзывы целиком — на странице города --}}
  <p class="mt-3 mb-0">
    <a href="{{ route('cities.show', $city) }}#comments"><i class="fa-solid fa-comments me-1"></i>Отзывы ({{ $city->comments->count() }})</a>
    @if ($city->comments->contains(fn ($c) => $friendIds->contains($c->user_id)))
      <span class="badge text-bg-success ms-1"><i class="fa-solid fa-user-group me-1"></i>есть от друзей</span>
    @endif
  </p>
  <small class="text-muted d-block mt-2">← → — переключение между городами</small>
</div>
<div class="modal-footer">
  {{-- кнопка видна только автору и администратору (у удалённого города редактирования нет) --}}
  @if (! $city->trashed())
    @can('modify-object', $city)
      <a href="{{ route('cities.edit', $city) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-pen me-1"></i>Редактировать</a>
    @endcan
  @endif
  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Закрыть</button>
</div>
