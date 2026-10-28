{{-- Подробный текст о городе: общий для окна Modal и страницы show --}}
<p class="text-muted mb-2">{{ $city->name_pt }} · округ {{ $city->district }}</p>
<p><strong>{{ $city->population_text }}</strong></p>
{{-- абзацы и слова с подсказками (см. description_html в модели) --}}
<div class="modal-text">{!! $city->description_html !!}</div>
<p class="small text-muted mb-0">Опубликовано {{ $city->published_at->format('d.m.Y') }}</p>
