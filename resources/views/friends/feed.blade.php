@extends('layouts.app')

@section('title', 'Лента')

@section('content')
  <h1>Лента</h1>
  <div class="row g-4 mb-5">
    {{-- мои друзья --}}
    <div class="col-lg-4">
      <div class="card city-details">
        <div class="card-body">
          <h2 class="h5 mt-0">Мои друзья ({{ $friends->count() }})</h2>
          @forelse ($friends as $friend)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <a href="{{ route('users.cities', $friend) }}"><i class="fa-solid fa-user me-1 text-muted"></i>{{ $friend->name }}</a>
              @include('users._friend_button', ['user' => $friend, 'isFriend' => true])
            </div>
          @empty
            <p class="text-muted mb-0">Друзей пока нет. Добавить можно на странице
              <a href="{{ route('users.index') }}">«Пользователи»</a>.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- новые города друзей: по дате добавления, новые сверху --}}
    <div class="col-lg-8">
      @forelse ($cities as $city)
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-4 col-md-3">
              <img src="{{ $city->image_url }}" class="img-fluid h-100 rounded-start feed-photo" alt="Вид на город {{ $city->name }}">
            </div>
            <div class="col-8 col-md-9">
              <div class="card-body">
                <p class="small text-muted mb-1">
                  <a href="{{ route('users.cities', $city->user) }}">{{ $city->user->name }}</a>
                  добавил город · {{ $city->created_at->format('d.m.Y H:i') }}
                </p>
                <h2 class="h5 my-1"><a href="{{ route('cities.show', $city) }}" class="text-reset">{{ $city->name }}</a></h2>
                <p class="card-text small mb-0">{{ $city->summary }}</p>
              </div>
            </div>
          </div>
        </div>
      @empty
        <p class="text-muted">В ленте пока пусто: здесь появятся города, которые добавят ваши друзья.</p>
      @endforelse
    </div>
  </div>
@endsection
