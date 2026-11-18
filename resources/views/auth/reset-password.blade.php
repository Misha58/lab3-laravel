@extends('layouts.app')

@section('title', 'Новый пароль')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1>Новый пароль</h1>
      <div class="card mb-5">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            @include('auth._field', ['name' => 'email', 'label' => 'Email', 'type' => 'email',
                'value' => old('email', $request->email), 'attrs' => 'required autocomplete="username"'])
            @include('auth._field', ['name' => 'password', 'label' => 'Новый пароль', 'type' => 'password',
                'attrs' => 'required minlength="8" autocomplete="new-password"'])
            @include('auth._field', ['name' => 'password_confirmation', 'label' => 'Пароль ещё раз', 'type' => 'password',
                'attrs' => 'required autocomplete="new-password"'])
            <button type="submit" class="btn btn-primary">Сохранить пароль</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
