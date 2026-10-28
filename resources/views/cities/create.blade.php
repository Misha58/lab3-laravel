@extends('layouts.app')

@section('title', 'Добавить город')

@section('content')
  <h1>Добавить город</h1>

  @include('cities._form', [
      'action' => route('cities.store'),
      'method' => 'POST',
  ])
@endsection
