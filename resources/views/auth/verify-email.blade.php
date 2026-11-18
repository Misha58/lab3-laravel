@extends('layouts.app')

@section('title', 'Подтверждение email')

@section('content')
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h1>Подтвердите email</h1>
      <div class="card mb-5">
        <div class="card-body p-4">
          <p>Мы отправили ссылку для подтверждения на вашу почту. Если письма нет, отправим ещё раз.</p>
          <div class="d-flex justify-content-between">
            <form method="POST" action="{{ route('verification.send') }}">
              @csrf
              <button type="submit" class="btn btn-primary">Отправить ещё раз</button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="btn btn-outline-secondary">Выйти</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
