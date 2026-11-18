@extends('layouts.app')

@section('title', 'Вход')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1>Вход</h1>
      <div class="card mb-5">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('login') }}">
            @csrf
            @include('auth._field', ['name' => 'email', 'label' => 'Email', 'type' => 'email',
                'value' => old('email'), 'attrs' => 'required autofocus autocomplete="username"'])
            @include('auth._field', ['name' => 'password', 'label' => 'Пароль', 'type' => 'password',
                'attrs' => 'required autocomplete="current-password"'])

            <div class="form-check mb-3">
              <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
              <label for="remember_me" class="form-check-label">Запомнить меня</label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="{{ route('password.request') }}" class="small">Забыли пароль?</a>
              <button type="submit" class="btn btn-primary">Войти</button>
            </div>
          </form>
        </div>
      </div>
      <p class="text-center">Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
    </div>
  </div>
@endsection
