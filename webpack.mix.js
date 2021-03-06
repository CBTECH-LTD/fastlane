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
    .copy('node_modules/tinymce/skins', 'public/skins')
    .copy('resources/icons', 'public/icons')
    .copy('resources/img', 'public/img')
    .js('resources/js/app.js', 'public')
    .extract([
        '@inertiajs/inertia',
        '@inertiajs/inertia-vue',
        'ajv',
        'axios',
        'lodash',
        'vue'
    ])
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

    // Copy files in the dev environment
    .copy('public', '../../public/vendor/fastlane')
