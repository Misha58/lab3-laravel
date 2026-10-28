<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Города Португалии')</title>
  <!-- стили, собранные Laravel Mix (public/css/main.css) -->
  <link rel="stylesheet" href="{{ mix('css/main.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- шапка -->
  <header class="site-header">
    <nav class="navbar navbar-expand-md navbar-dark">
      <div class="container">
        <a class="navbar-brand brand" href="{{ route('cities.index') }}">
          <span class="brand__logo">П</span>
          <span class="brand__name">Города Португалии</span>
        </a>
        <!-- кнопка-бургер для узких экранов -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Открыть меню">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav ms-auto align-items-md-center gap-md-2">
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('cities.index') ? 'active' : '' }}" href="{{ route('cities.index') }}">Города</a>
            </li>
            <li class="nav-item">
              <a class="btn-cta d-inline-block text-decoration-none" href="{{ route('cities.create') }}">
                <i class="fa-solid fa-plus me-1"></i>Добавить
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main class="container flex-grow-1">
    @yield('content')
  </main>

  <footer class="site-footer">
    <div class="container footer-inner">
      <div class="footer-divider"></div>
      <div class="footer-content">
        <p class="footer-name">Баширов Михаил</p>
        <div class="footer-social">
          <a class="social-icon" href="#" aria-label="Telegram">
            <img src="https://static.vecteezy.com/system/resources/previews/023/986/971/large_2x/telegram-logo-telegram-logo-transparent-telegram-icon-transparent-free-free-png.png" alt="Telegram">
          </a>
          <a class="social-icon" href="#" aria-label="VKontakte">
            <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/VK_Logo.png" alt="VKontakte">
          </a>
          <a class="social-icon" href="#" aria-label="ICQ">
            <img src="https://images.icon-icons.com/622/PNG/512/icq-flower-logo_icon-icons.com_57128.png" alt="ICQ">
          </a>
        </div>
      </div>
    </div>
  </footer>

  <!-- тост: сообщение после сохранения / удаления (текст приходит из сессии) -->
  @if (session('status'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div id="flashToast" class="toast" role="alert">
        <div class="toast-header">
          <i class="fa-solid fa-circle-check toast-icon me-2"></i>
          <strong class="me-auto">Готово</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
        </div>
        <div class="toast-body">{{ session('status') }}</div>
      </div>
    </div>
  @endif

  <!-- скрипты, собранные Laravel Mix (public/js/app.js) -->
  <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
