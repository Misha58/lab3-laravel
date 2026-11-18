@extends('layouts.app')

@section('title', 'Пользователи')

@section('content')
  <h1>Пользователи</h1>

  {{-- список пользователей: ссылка ведёт на /users/{имя}/cities --}}
  <div class="list-group mb-5" style="max-width: 640px">
    @forelse ($users as $user)
      <a href="{{ route('users.cities', $user) }}"
         class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
        <span>
          <i class="fa-solid fa-user me-2 text-muted"></i>{{ $user->name }}
          @if ($user->is_admin)
            <span class="badge text-bg-warning ms-1">админ</span>
          @endif
          @if (Auth::id() === $user->id)
            <span class="badge text-bg-secondary ms-1">это вы</span>
          @endif
        </span>
        <span>
          <span class="badge text-bg-primary rounded-pill">городов: {{ $user->cities_count }}</span>
          {{-- число удалённых показываем только администратору --}}
          @can('admin')
            @if ($user->trashed_count)
              <span class="badge text-bg-danger rounded-pill">удалено: {{ $user->trashed_count }}</span>
            @endif
          @endcan
        </span>
      </a>
    @empty
      <p>Пока никто не зарегистрировался.</p>
    @endforelse
  </div>
@endsection
