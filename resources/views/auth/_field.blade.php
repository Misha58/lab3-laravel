{{-- Одно поле формы входа/регистрации с текстом ошибки.
     Параметры: $name, $label, $type (по умолчанию text), $value, $attrs (доп. атрибуты), $help --}}
<div class="mb-3">
  <label for="{{ $name }}" class="form-label">{{ $label }}</label>
  <input id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}"
         class="form-control @error($name, $bag ?? 'default') is-invalid @enderror"
         value="{{ $value ?? '' }}" {!! $attrs ?? '' !!}>
  @isset($help)
    <div class="form-text">{{ $help }}</div>
  @endisset
  @error($name, $bag ?? 'default')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
