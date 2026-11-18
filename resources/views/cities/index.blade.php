@extends('layouts.app')

@section('title', $owner ? 'Города пользователя ' . $owner->name : 'Города Португалии')

@section('content')
  {{-- $owner — пользователь, чьи города смотрим; null — города всех пользователей --}}
  @if ($owner)
    <p class="mt-4 mb-0"><a href="{{ route('users.index') }}"><i class="fa-solid fa-arrow-left me-1"></i>Все пользователи</a></p>
    <h1 class="mt-2">Города пользователя {{ $owner->name }}</h1>
  @else
    <h1>Города Португалии</h1>
  @endif

  {{-- подсказка, если в списке есть удалённые города --}}
  @if ($cities->contains(fn ($city) => $city->trashed()))
    <p class="text-muted">Удалённые города отмечены красной рамкой.
      @cannot('admin') Восстановить их может только администратор. @endcannot
    </p>
  @endif

  @if ($cities->isEmpty())
    <p class="text-center my-5">Пока нет ни одного города.
      @auth <a href="{{ route('cities.create') }}">Добавить первый</a> @endauth
    </p>
  @endif

  <!-- сетка: 1 / 2 / 3 / 4 / 5 карточек в ряд на xs / sm / lg / xxl / xxxl -->
  <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xxl-4 row-cols-xxxl-5 pb-5" id="cities">
    @foreach ($cities as $city)
      <div class="col">
        {{-- удалённый город: красная рамка и полупрозрачная картинка --}}
        <div class="card h-100 {{ $city->trashed() ? 'border-danger border-2' : '' }}">
          <div class="image-container {{ $city->trashed() ? 'opacity-50' : '' }}">
            <img src="{{ $city->image_url }}" alt="Вид на город {{ $city->name }}" class="card-img-top img-fluid">
            <span class="badge">{{ $city->trashed() ? 'удалён' : 'город' }}</span>
          </div>
          <div class="card-body card-content d-flex flex-column">
            <h2 class="card-title">
              <a href="{{ route('cities.show', $city) }}" class="text-reset text-decoration-none">{{ $city->name }}</a>
            </h2>
            <p class="card-text">{{ $city->summary }}</p>
            <p class="card-text">{{ $city->population_text }}</p>
            <!-- автор города: ссылка на его список -->
            <p class="card-text small text-muted">
              Добавил:
              @if ($city->user)
                <a href="{{ route('users.cities', $city->user) }}">{{ $city->user->name }}</a>
              @else
                неизвестно
              @endif
            </p>
            <div class="mt-auto pt-2 d-flex flex-wrap gap-2">
              <!-- «Подробнее»: действие show, открывается в модальном окне -->
              <a href="{{ route('cities.show', $city) }}" class="btn btn-primary js-show">Подробнее</a>

              @if ($city->trashed())
                {{-- удалённый город: кнопки только у администратора --}}
                @include('cities._admin_buttons')
              @else
                {{-- кнопки видны, только если Gate разрешает (автор или админ) --}}
                @can('modify-object', $city)
                  <a href="{{ route('cities.edit', $city) }}" class="btn btn-outline-secondary" title="Редактировать">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                  <!-- мягкое удаление: форма с методом DELETE -->
                  <form action="{{ route('cities.destroy', $city) }}" method="POST" class="js-delete">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="back" value="{{ url()->current() }}">
                    <button type="submit" class="btn btn-outline-danger" title="Удалить"><i class="fa-solid fa-trash"></i></button>
                  </form>
                @endcan
              @endif
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
