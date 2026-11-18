@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1>Регистрация</h1>
      <div class="card mb-5">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('register') }}">
            @csrf
            @include('auth._field', ['name' => 'name', 'label' => 'Имя пользователя',
                'value' => old('name'), 'help' => 'Латиница, цифры, - и _. Будет в адресе вашей страницы.',
                'attrs' => 'required autofocus autocomplete="username" minlength="3" maxlength="30" pattern="[A-Za-z0-9_\-]+"'])
            @include('auth._field', ['name' => 'email', 'label' => 'Email', 'type' => 'email',
                'value' => old('email'), 'attrs' => 'required autocomplete="email"'])
            @include('auth._field', ['name' => 'password', 'label' => 'Пароль', 'type' => 'password',
                'help' => 'Не меньше 8 символов.', 'attrs' => 'required minlength="8" autocomplete="new-password"'])
            @include('auth._field', ['name' => 'password_confirmation', 'label' => 'Пароль ещё раз', 'type' => 'password',
                'attrs' => 'required autocomplete="new-password"'])

            <div class="d-flex justify-content-between align-items-center">
              <a href="{{ route('login') }}" class="small">Уже есть аккаунт?</a>
              <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
