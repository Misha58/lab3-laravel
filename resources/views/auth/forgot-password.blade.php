@extends('layouts.app')

@section('title', 'Сброс пароля')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1>Забыли пароль?</h1>
      <div class="card mb-5">
        <div class="card-body p-4">
          <p class="text-muted">Укажите email, и мы пришлём ссылку для нового пароля.
            (В учебном проекте письмо пишется в storage/logs/laravel.log.)</p>
          <form method="POST" action="{{ route('password.email') }}">
            @csrf
            @include('auth._field', ['name' => 'email', 'label' => 'Email', 'type' => 'email',
                'value' => old('email'), 'attrs' => 'required autofocus'])
            <button type="submit" class="btn btn-primary">Отправить ссылку</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
