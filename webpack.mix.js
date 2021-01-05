const mix = require('laravel-mix').setPublicPath('public');

// scss
mix
  .sass('resources/sass/cards.scss', 'public/lib/css/ui/v1.1/cards.css')
  .sass('resources/sass/full.style.scss', 'public/lib/css/ui/v1.1/full.style.css')
  .sass('resources/sass/style.scss', 'public/lib/css/ui/v1.1/style.css')
  .sass('resources/sass/widgets.scss', 'public/lib/css/ui/v1.1/widgets.css')

  .options({ autoprefixer: false });

// copy js folder
mix.copy('resources/js', 'public/lib/js');

// js bundles
mix.combine([
  'resources/js/index.js',
  'resources/js/bundles/keepalive.js',
  'resources/js/bundles/message.js',
], 'public/lib/js/bundles.js');
