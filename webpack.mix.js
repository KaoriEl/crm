const mix = require('laravel-mix');

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

mix.options({ processCssUrls: false });

mix.setPublicPath('public_html/');

mix.js('resources/js/app.js', 'js')
    .js('resources/js/generateLinks.js', 'js')
    .js('resources/js/addTableStatistic.js', 'js')
   .extract([
       'jquery',
       'popper.js',
       'dropzone',
       'datatables.net',
       'datatables.net-bs4',
       'jquery-datetimepicker',
       'bootstrap',
       'pusher-js',
       'laravel-echo',
   ])
   .sass('resources/sass/app.scss', 'css')
   .version()
   .disableNotifications();
