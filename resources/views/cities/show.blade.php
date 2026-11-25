@extends('layouts.app')

@section('title', $city->name)

@section('content')
  <p class="mt-4"><a href="{{ route('cities.index') }}"><i class="fa-solid fa-arrow-left me-1"></i>Ко всем городам</a></p>

  <div class="card city-details mb-5">
    <div class="card-body p-4">
      <h1 class="mt-0">{{ $city->name }}</h1>
      @if ($city->trashed())
        <div class="alert alert-danger">Этот город удалён {{ $city->deleted_at->format('d.m.Y') }}.</div>
      @endif
      <div class="row g-4">
        <div class="col-md-5">
          <img src="{{ $city->image_url }}" class="img-fluid rounded w-100 modal-photo" alt="Вид на город {{ $city->name }}">
        </div>
        <div class="col-md-7">
          @include('cities._info')

          <div class="d-flex flex-wrap gap-2 mt-4">
            @if ($city->trashed())
              {{-- удалённый город: только администратор может восстановить или стереть --}}
              @include('cities._admin_buttons', ['back' => route('cities.index')])
            @else
              @can('modify-object', $city)
                <a href="{{ route('cities.edit', $city) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-pen me-1"></i>Редактировать</a>
                <form action="{{ route('cities.destroy', $city) }}" method="POST" class="js-delete">
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="back" value="{{ route('cities.index') }}">
                  <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Удалить</button>
                </form>
              @endcan
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- отзывы о городе (вспомогательная сущность) --}}
  @include('cities._comments')
@endsection
