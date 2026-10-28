@extends('layouts.app')

@section('content')
  <h1>Города Португалии</h1>

  @if ($cities->isEmpty())
    <p class="text-center my-5">Пока нет ни одного города. <a href="{{ route('cities.create') }}">Добавить первый</a></p>
  @endif

  <!-- сетка: 1 / 2 / 3 / 4 / 5 карточек в ряд на xs / sm / lg / xxl / xxxl -->
  <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xxl-4 row-cols-xxxl-5 pb-5" id="cities">
    @foreach ($cities as $city)
      <div class="col">
        <div class="card h-100">
          <div class="image-container">
            <img src="{{ $city->image_url }}" alt="Вид на город {{ $city->name }}" class="card-img-top img-fluid">
            <span class="badge">город</span>
          </div>
          <div class="card-body card-content d-flex flex-column">
            <h2 class="card-title">
              <a href="{{ route('cities.show', $city) }}" class="text-reset text-decoration-none">{{ $city->name }}</a>
            </h2>
            <p class="card-text">{{ $city->summary }}</p>
            <p class="card-text">{{ $city->population_text }}</p>
            <div class="mt-auto pt-2 d-flex flex-wrap gap-2">
              <!-- «Подробнее»: действие show, открывается в модальном окне -->
              <a href="{{ route('cities.show', $city) }}" class="btn btn-primary js-show">Подробнее</a>
              <a href="{{ route('cities.edit', $city) }}" class="btn btn-outline-secondary" title="Редактировать">
                <i class="fa-solid fa-pen"></i>
              </a>
              <!-- удаление: форма с методом DELETE -->
              <form action="{{ route('cities.destroy', $city) }}" method="POST" class="js-delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" title="Удалить"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- модальное окно: содержимое загружается из действия show (cities/_details.blade.php) -->
  <div class="modal fade" id="cityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content"></div>
    </div>
  </div>
@endsection
