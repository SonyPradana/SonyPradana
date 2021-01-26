const mix = require('laravel-mix').setPublicPath('public');

// scss
mix
  .sass('resources/sass/cards.scss', 'public/lib/css/ui/v1.1/cards.css')
  .sass('resources/sass/full.style.scss', 'public/lib/css/ui/v1.1/full.style.css')
  .sass('resources/sass/style.scss', 'public/lib/css/ui/v1.1/style.css')
  .sass('resources/sass/widgets.scss', 'public/lib/css/ui/v1.1/widgets.css')
  .sass('resources/sass/tailwind-colors.scss', 'public/lib/css/ui/v1.1/tailwind-colors.css')

  .options({ autoprefixer: false });

// copy js folder
mix.copy('resources/js', 'public/lib/js');

// js bundles
mix.combine([
  'resources/js/index.js',
  'resources/js/bundles/keepalive.js',
  'resources/js/bundles/message.js',
], 'public/lib/js/bundles.js');

// vue
mix.js('resources/vue/app.js', 'public/vue')
.postCss("resources/vue/css/app.css", "public/vue/css", [
  require("tailwindcss"),
 ])
