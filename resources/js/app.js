// ГЛАВНЫЙ JS-ФАЙЛ: собирается Laravel Mix в public/js/app.js

// библиотеки
import $ from 'jquery';
// Collapse явно не вызывается, но нужен для работы бургер-меню
import { Collapse, Modal, Popover, Toast } from 'bootstrap';

// делаем jQuery глобальным (как в лабе 2)
window.$ = window.jQuery = $;

// ---------- ОКНО «ПОДРОБНЕЕ» ----------

let popovers = []; // активные подсказки в окне

// Загружает подробную информацию (действие show контроллера) и показывает её в окне Modal
function openDetails(url) {
  $.ajax({ url: url, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .done(function (html) {
      popovers.forEach(p => p.dispose());               // убираем старые подсказки
      $('#cityModal .modal-content').html(html);         // вставляем blade-представление _details
      $('#cityModal').data('url', url);                  // запоминаем, какой город открыт

      // включаем подсказку на каждом слове с классом hint
      popovers = $('#cityModal .hint').map(function () {
        return new Popover(this, { trigger: 'hover focus', placement: 'top' });
      }).get();

      Modal.getOrCreateInstance('#cityModal').show();
    });
}

// Переключает окно на соседний город: step = 1 (вправо) или -1 (влево)
function switchDetails(step) {
  const $modal = $('#cityModal');
  if (!$modal.hasClass('show')) return;               // окно закрыто — ничего не делаем

  const urls = $('.js-show').map(function () { return this.href; }).get();
  const index = urls.indexOf($modal.data('url'));
  if (index === -1) return;

  const next = (index + step + urls.length) % urls.length; // после последнего снова первый
  openDetails(urls[next]);
}

// ---------- ПРОВЕРКА ФОРМЫ ПРИ ВВОДЕ ----------
// Простые правила (обязательность, длина, шаблон, min/max) проверяет сам браузер по атрибутам HTML5.
// Здесь добавляем правила, которые зависят от нескольких полей, и показываем результат сразу при вводе.

// Возвращает число из поля без пробелов или null, если поле пустое
function numberOf(id) {
  const value = String($('#' + id).val() || '').replace(/\s/g, '');
  return value ? Number(value) : null;
}

// Проверки, которые HTML5 сделать не может. Возвращает текст ошибки или '' если всё хорошо
function extraError(field) {
  switch (field.id) {
    case 'city_population':
    case 'municipality_population': {
      const value = numberOf(field.id);
      if (value !== null && value > 10000000) return 'Слишком большое число: не больше 10 000 000.';
      const city = numberOf('city_population');
      const municipality = numberOf('municipality_population');
      if (field.id === 'municipality_population' && city && municipality && municipality < city) {
        return 'Население муниципалитета не может быть меньше населения города.';
      }
      break;
    }
    case 'summary':
    case 'description':
      if (/<[^>]*>/.test(field.value)) return 'Нельзя использовать HTML-теги.';
      break;
  }
  return '';
}

// Проверяет одно поле и сразу показывает результат
function checkField(field) {
  field.setCustomValidity(extraError(field)); // своя ошибка (или '' — ошибки нет)
  showState(field);
}

// Красит поле (зелёная или красная рамка) и пишет текст под ним
function showState(field) {
  const $field = $(field);
  const $feedback = $field.closest('[class*="col"]').find('.invalid-feedback');
  const valid = field.checkValidity();

  $field.toggleClass('is-invalid', !valid).toggleClass('is-valid', valid);
  // текст: наша ошибка, если она есть, иначе обычная подсказка для этого поля
  $feedback.text(field.validity.customError ? field.validationMessage : $feedback.data('hint'));
}

// Проверяет фото: вес файла и размеры в пикселях
function checkImage(field) {
  const file = field.files[0];
  field.setCustomValidity('');

  if (!file) { showState(field); return; }
  if (file.size > 4 * 1024 * 1024) {
    field.setCustomValidity('Фото должно весить не больше 4 МБ.');
    showState(field);
    return;
  }
  const img = new Image();
  img.onload = function () {
    if (img.width < 300 || img.height < 200) {
      field.setCustomValidity('Фото слишком маленькое: нужно не меньше 300×200 пикселей.');
    }
    showState(field);
  };
  img.onerror = function () {
    field.setCustomValidity('Файл должен быть картинкой.');
    showState(field);
  };
  img.src = URL.createObjectURL(file);
}

// Подключает проверку к форме
function initFormValidation() {
  const $form = $('.needs-validation');
  if ($form.length === 0) return;

  const fields = 'input:not([type=hidden]):not([type=file]), textarea';

  // проверка при каждом вводе символа и при выходе из поля
  $form.on('input change blur', fields, function () {
    checkField(this);
    // население города и муниципалитета зависят друг от друга — перепроверяем соседнее поле
    if (this.id === 'city_population' && $('#municipality_population').val()) checkField($('#municipality_population')[0]);
    if (this.id === 'municipality_population' && $('#city_population').val()) checkField($('#city_population')[0]);
  });

  $form.on('change', '#image', function () { checkImage(this); });

  // при отправке проверяем все поля и не отправляем форму с ошибками
  $form.on('submit', function (e) {
    $(this).find(fields).each(function () { checkField(this); });
    $(this).find('#image').each(function () { showState(this); });
    if (!this.checkValidity()) {
      e.preventDefault();
      $(this).find('.is-invalid').first().trigger('focus'); // курсор на первую ошибку
    }
  });
}

// Показывает выбранное фото до отправки формы
function initImagePreview() {
  $('#image').on('change', function () {
    const file = this.files[0];
    if (file) $('#imagePreview').attr('src', URL.createObjectURL(file)).removeClass('d-none');
  });
}

// ---------- ЗАПУСК ПОСЛЕ ЗАГРУЗКИ СТРАНИЦЫ ----------
$(function () {
  initFormValidation();
  initImagePreview();

  // подсказки на странице show (там текст уже на странице, без AJAX)
  $('main .hint').each(function () {
    new Popover(this, { trigger: 'hover focus', placement: 'top' });
  });

  // кнопка «Подробнее»: открываем show в модальном окне вместо перехода на страницу
  $(document).on('click', '.js-show', function (e) {
    e.preventDefault();
    openDetails(this.href);
  });

  // стрелки листают города, пока окно открыто
  $(document).on('keydown', function (e) {
    if (e.key === 'ArrowRight') switchDetails(1);
    if (e.key === 'ArrowLeft') switchDetails(-1);
  });

  // при закрытии окна прячем подсказки
  $('#cityModal').on('hidden.bs.modal', () => popovers.forEach(p => p.hide()));

  // кнопка «Удалить» спрашивает подтверждение
  $(document).on('submit', '.js-delete', function (e) {
    if (!confirm('Удалить этот город?')) e.preventDefault();
  });

  // тост после сохранения / удаления
  if ($('#flashToast').length) {
    Toast.getOrCreateInstance('#flashToast').show();
  }
});
