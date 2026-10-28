@extends('layouts.app')

@section('title', 'Редактировать: ' . $city->name)

@section('content')
  <h1>Редактировать: {{ $city->name }}</h1>

  @include('cities._form', [
      'action' => route('cities.update', $city),
      'method' => 'PUT',
  ])
@endsection
