const colors = require('tailwindcss/colors')

module.exports = {
  purge: [

    './resources/vue/**/*.js',

    './resources/vue/**/*.vue',

    './app/views/admin/*.template.php',

  ],
  darkMode: 'media', // or 'media' or 'class'
  theme: {
    extend: {
      gridTemplateColumns: {
        'dashbord': '240px 1fr'
      },
      gridTemplateRows: {
        'dashbord': '56px 1fr'
      },
      colors: {
        // Build your palette here
        gray: colors.trueGray,
        blue: colors.fuchsia,
        lime: colors.lime,
        rose: colors.rose,
        cyan: colors.cyan,
      }
    },    
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
