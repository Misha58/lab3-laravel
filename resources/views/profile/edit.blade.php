@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
  <h1>Профиль</h1>

  <div class="row g-4 mb-5" style="max-width: 720px">
    <!-- имя и email -->
    <div class="col-12">
      <div class="card"><div class="card-body p-4">
        <h2 class="h5">Данные профиля</h2>
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PATCH')
          @include('auth._field', ['name' => 'name', 'label' => 'Имя пользователя', 'value' => old('name', $user->name),
              'help' => 'Если поменять имя, поменяется и адрес вашей страницы.', 'attrs' => 'required'])
          @include('auth._field', ['name' => 'email', 'label' => 'Email', 'type' => 'email',
              'value' => old('email', $user->email), 'attrs' => 'required'])
          <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
      </div></div>
    </div>

    <!-- смена пароля -->
    <div class="col-12">
      <div class="card"><div class="card-body p-4">
        <h2 class="h5">Смена пароля</h2>
        <form method="POST" action="{{ route('password.update') }}">
          @csrf
          @method('PUT')
          @include('auth._field', ['name' => 'current_password', 'bag' => 'updatePassword', 'label' => 'Текущий пароль',
              'type' => 'password', 'attrs' => 'autocomplete="current-password"'])
          @include('auth._field', ['name' => 'password', 'bag' => 'updatePassword', 'label' => 'Новый пароль',
              'type' => 'password', 'attrs' => 'autocomplete="new-password"'])
          @include('auth._field', ['name' => 'password_confirmation', 'bag' => 'updatePassword', 'label' => 'Новый пароль ещё раз',
              'type' => 'password', 'attrs' => 'autocomplete="new-password"'])
          <button type="submit" class="btn btn-primary">Сменить пароль</button>
        </form>
      </div></div>
    </div>

    <!-- удаление аккаунта -->
    <div class="col-12">
      <div class="card border-danger"><div class="card-body p-4">
        <h2 class="h5">Удалить аккаунт</h2>
        <p class="text-muted">Аккаунт удалится навсегда. Ваши города останутся на сайте без автора.</p>
        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Точно удалить аккаунт?')">
          @csrf
          @method('DELETE')
          @include('auth._field', ['name' => 'password', 'bag' => 'userDeletion', 'label' => 'Пароль для подтверждения',
              'type' => 'password', 'attrs' => 'required'])
          <button type="submit" class="btn btn-danger">Удалить аккаунт</button>
        </form>
      </div></div>
    </div>
  </div>
@endsection
