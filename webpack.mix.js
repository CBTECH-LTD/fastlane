const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Admin resources

mix
    .setPublicPath('public')
    .copy('resources/fonts', 'public/fonts')
    .copy('resources/icons', 'public/icons')
    .copy('resources/img', 'public/img')
    .js('resources/js/app.js', 'public')
    // .extract([
    //     'ajv',
    //     'axios',
    //     'lodash',
    //     'vue'
    // ])
    .postCss('resources/css/app.css', 'public', [
        require('tailwindcss')('resources/tailwind.config.js')
    ])
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js/'),
            },
        },
    })
    .version()
    .copy('public', '../../public/vendor/fastlane')
