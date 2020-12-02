const mix = require('laravel-mix').setPublicPath('public');

// scss
mix
  .sass('lib/scss/cards.scss', 'public/lib/css/ui/v1.1/cards.css')
  .sass('lib/scss/full.style.scss', 'public/lib/css/ui/v1.1/full.style.css')
  .sass('lib/scss/style.scss', 'public/lib/css/ui/v1.1/style.css')
  .sass('lib/scss/widgets.scss', 'public/lib/css/ui/v1.1/widgets.css')

  .options({ autoprefixer: false });

// copy js folder
mix.copy('lib/js', 'public/lib/js');

// js bundles
mix.combine([
  'lib/js/index.js',
  'lib/js/bundles/keepalive.js',
  'lib/js/bundles/message.js',
], 'public/lib/js/bundles.js');
