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
  <small class="text-muted d-block mt-3">← → — переключение между городами</small>
</div>
<div class="modal-footer">
  <a href="{{ route('cities.edit', $city) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-pen me-1"></i>Редактировать</a>
  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Закрыть</button>
</div>
