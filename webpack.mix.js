const mix = require('laravel-mix')
const path = require('path')

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
    .postCss('resources/css/app.css', 'public', [
        require('tailwindcss')('resources/tailwind.config.js')
    ])
    .js('resources/js/app.js', 'public')
    // .extract([
    //     // 'ajv',
    //     'axios',
    //     // 'lodash',
    //     'slim-select',
        // '@hotwired/turbo'
    // ])
    .webpackConfig({
        output: {
            filename: '[name].js',
            chunkFilename: '[name].js',
            publicPath: '/vendor/fastlane/'
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js/'),
            },
        },
    })
    .version()
    .copy('public', '../../public/vendor/fastlane')
