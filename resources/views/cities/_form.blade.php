{{-- ОБЩАЯ ФОРМА для добавления (create) и редактирования (edit).
     $action — куда отправлять, $method — POST или PUT.
     Простые правила проверяет браузер по атрибутам HTML5 (required, pattern, min, max),
     сложные — app.js прямо при вводе, и всё ещё раз — сервер (CityRequest). --}}
<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="obj-form needs-validation mb-5" novalidate>
  @csrf
  @if ($method === 'PUT')
    @method('PUT')
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">Проверьте поля формы — некоторые заполнены неверно.</div>
  @endif

  {{-- В каждом поле: текст ошибки с сервера, а если её нет — подсказка (data-hint) для проверки в браузере --}}
  <div class="row g-3">
    <!-- Название -->
    <div class="col-md-4">
      <label for="name" class="form-label">Название *</label>
      <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $city->name) }}" required minlength="2" maxlength="100"
             pattern="[А-ЯЁ][а-яёА-ЯЁ]*([\s\-][А-ЯЁа-яё][а-яёА-ЯЁ]*)*" placeholder="Агуалва-Касен">
      <div class="invalid-feedback" data-hint="Русскими буквами, с заглавной.">{{ $errors->first('name') ?: 'Русскими буквами, с заглавной.' }}</div>
    </div>

    <!-- Название на португальском -->
    <div class="col-md-4">
      <label for="name_pt" class="form-label">Название на португальском *</label>
      <input type="text" id="name_pt" name="name_pt" class="form-control @error('name_pt') is-invalid @enderror"
             value="{{ old('name_pt', $city->name_pt) }}" required minlength="2" maxlength="100"
             pattern="[A-ZÀ-Ý][A-Za-zÀ-ÿ']*([\s\-][A-Za-zÀ-ÿ][A-Za-zÀ-ÿ']*)*" placeholder="Agualva-Cacém">
      <div class="invalid-feedback" data-hint="Латиницей, с заглавной.">{{ $errors->first('name_pt') ?: 'Латиницей, с заглавной.' }}</div>
    </div>

    <!-- Округ -->
    <div class="col-md-4">
      <label for="district" class="form-label">Округ *</label>
      <input type="text" id="district" name="district" class="form-control @error('district') is-invalid @enderror"
             value="{{ old('district', $city->district) }}" required minlength="2" maxlength="100"
             pattern="[А-ЯЁ][а-яёА-ЯЁ]*([\s\-][А-ЯЁа-яё][а-яёА-ЯЁ]*)*" placeholder="Лиссабон">
      <div class="invalid-feedback" data-hint="Русскими буквами, с заглавной.">{{ $errors->first('district') ?: 'Русскими буквами, с заглавной.' }}</div>
    </div>

    <!-- Население города -->
    <div class="col-sm-6 col-lg-3">
      <label for="city_population" class="form-label">Население города</label>
      <input type="text" id="city_population" name="city_population" inputmode="numeric"
             class="form-control @error('city_population') is-invalid @enderror"
             value="{{ old('city_population', $city->city_population) }}" pattern="[1-9][0-9 ]*" maxlength="12" placeholder="57 000">
      <div class="invalid-feedback" data-hint="Только цифры (пробелы можно).">{{ $errors->first('city_population') ?: 'Только цифры (пробелы можно).' }}</div>
    </div>

    <!-- Население муниципалитета -->
    <div class="col-sm-6 col-lg-3">
      <label for="municipality_population" class="form-label">Население муниципалитета</label>
      <input type="text" id="municipality_population" name="municipality_population" inputmode="numeric"
             class="form-control @error('municipality_population') is-invalid @enderror"
             value="{{ old('municipality_population', $city->municipality_population) }}" pattern="[1-9][0-9 ]*" maxlength="12" placeholder="78 455">
      <div class="invalid-feedback" data-hint="Только цифры (пробелы можно).">{{ $errors->first('municipality_population') ?: 'Только цифры (пробелы можно).' }}</div>
    </div>

    <!-- Год переписи -->
    <div class="col-sm-6 col-lg-3">
      <label for="census_year" class="form-label">Год переписи</label>
      <input type="number" id="census_year" name="census_year" class="form-control @error('census_year') is-invalid @enderror"
             value="{{ old('census_year', $city->census_year) }}" min="1864" max="{{ date('Y') }}" step="1" placeholder="2011">
      <div class="form-text">Можно не указывать.</div>
      <div class="invalid-feedback" data-hint="От 1864 до {{ date('Y') }}.">{{ $errors->first('census_year') ?: 'От 1864 до ' . date('Y') . '.' }}</div>
    </div>

    <!-- Дата публикации -->
    <div class="col-sm-6 col-lg-3">
      <label for="published_at" class="form-label">Дата публикации *</label>
      <input type="date" id="published_at" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
             value="{{ old('published_at', $city->published_at?->format('Y-m-d')) }}" required min="2000-01-01" max="{{ now()->format('Y-m-d') }}">
      <div class="invalid-feedback" data-hint="С 2000 года и не в будущем.">{{ $errors->first('published_at') ?: 'С 2000 года и не в будущем.' }}</div>
    </div>

    <!-- Краткое описание -->
    <div class="col-12">
      <label for="summary" class="form-label">Краткое описание для карточки *</label>
      <textarea id="summary" name="summary" rows="2" class="form-control @error('summary') is-invalid @enderror"
                required minlength="20" maxlength="300">{{ old('summary', $city->summary) }}</textarea>
      <div class="invalid-feedback" data-hint="От 20 до 300 символов, без HTML-тегов.">{{ $errors->first('summary') ?: 'От 20 до 300 символов, без HTML-тегов.' }}</div>
    </div>

    <!-- Подробное описание -->
    <div class="col-12">
      <label for="description" class="form-label">Подробное описание (для окна «Подробнее») *</label>
      <textarea id="description" name="description" rows="6" class="form-control @error('description') is-invalid @enderror"
                required minlength="50" maxlength="5000">{{ old('description', $city->description) }}</textarea>
      <div class="form-text">Абзацы разделяйте пустой строкой. Слово с подсказкой: <code>[Тежу|Самая длинная река полуострова]</code></div>
      <div class="invalid-feedback" data-hint="От 50 до 5000 символов, без HTML-тегов.">{{ $errors->first('description') ?: 'От 50 до 5000 символов, без HTML-тегов.' }}</div>
    </div>

    <!-- Фото -->
    <div class="col-12">
      <label for="image" class="form-label">Фото {{ $city->exists ? '' : '*' }}</label>
      <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
             class="form-control @error('image') is-invalid @enderror" {{ $city->exists ? '' : 'required' }}>
      <div class="form-text">jpg, png или webp до 4 МБ, не меньше 300×200 пикселей. Фото будет обрезано до 600×400.
        @if ($city->exists) Если не выбирать файл, останется текущее фото. @endif
      </div>
      <div class="invalid-feedback" data-hint="Выберите фото.">{{ $errors->first('image') ?: 'Выберите фото.' }}</div>
      {{-- превью: текущее фото или только что выбранный файл --}}
      <img id="imagePreview" src="{{ $city->image_url }}" alt="Превью фото"
           class="obj-form__preview mt-3 {{ $city->image_url ? '' : 'd-none' }}">
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Сохранить</button>
    <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary">Отмена</a>
  </div>
</form>
