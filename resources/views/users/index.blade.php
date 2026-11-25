@extends('layouts.app')

@section('title', 'Пользователи')

@section('content')
  <h1>Пользователи</h1>

  {{-- список пользователей: имя ведёт на /users/{имя}/cities, справа — кнопка дружбы --}}
  <div class="list-group mb-5" style="max-width: 760px">
    @forelse ($users as $user)
      <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>
          <a href="{{ route('users.cities', $user) }}" class="fw-semibold">
            <i class="fa-solid fa-user me-2 text-muted"></i>{{ $user->name }}
          </a>
          @if ($user->is_admin)
            <span class="badge text-bg-warning ms-1">админ</span>
          @endif
          @if (Auth::id() === $user->id)
            <span class="badge text-bg-secondary ms-1">это вы</span>
          @endif
          @if ($friendIds->contains($user->id))
            <span class="badge text-bg-success ms-1"><i class="fa-solid fa-user-group me-1"></i>друг</span>
          @endif
        </span>
        <span class="d-flex flex-wrap align-items-center gap-2">
          <span class="badge text-bg-primary rounded-pill">городов: {{ $user->cities_count }}</span>
          <span class="badge text-bg-light rounded-pill">друзей: {{ $user->friends_count }}</span>
          {{-- число удалённых показываем только администратору --}}
          @can('admin')
            @if ($user->trashed_count)
              <span class="badge text-bg-danger rounded-pill">удалено: {{ $user->trashed_count }}</span>
            @endif
          @endcan
          @include('users._friend_button', ['isFriend' => $friendIds->contains($user->id)])
        </span>
      </div>
    @empty
      <p>Пока никто не зарегистрировался.</p>
    @endforelse
  </div>
@endsection
