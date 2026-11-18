@extends('layouts.app')

@section('title', 'Подтвердите пароль')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1>Подтвердите пароль</h1>
      <div class="card mb-5">
        <div class="card-body p-4">
          <p class="text-muted">Это защищённая часть сайта, введите пароль ещё раз.</p>
          <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            @include('auth._field', ['name' => 'password', 'label' => 'Пароль', 'type' => 'password',
                'attrs' => 'required autocomplete="current-password"'])
            <button type="submit" class="btn btn-primary">Подтвердить</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
