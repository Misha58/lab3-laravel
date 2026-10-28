// Настройки сборки Laravel Mix (обёртка над Webpack)
// Исходники лежат в resources/, результат сборки — в public/
const mix = require('laravel-mix');

mix
  // JS: Bootstrap + jQuery + наш код -> public/js/app.js
  .js('resources/js/app.js', 'public/js')

  // SCSS: Bootstrap из исходников + наши стили + иконки -> public/css/main.css
  .sass('resources/scss/main.scss', 'public/css', {
    sassOptions: {
      // скрываем предупреждения о старом синтаксисе внутри Bootstrap и Sass
      quietDeps: true,
      silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function', 'legacy-js-api', 'mixed-decls'],
    },
  })

  // карты исходников, чтобы в браузере видеть исходные scss/js
  .sourceMaps(false, 'source-map')

  // добавляем к файлам ?id=хеш, чтобы браузер не брал старую версию из кеша
  .version()

  // не показывать системные уведомления после каждой сборки
  .disableNotifications();
