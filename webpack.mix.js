let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

// mix.js('src/app.js', 'dist/')
//    .sass('src/app.scss', 'dist/');

// Full API
// mix.js(src, output);
// mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
// mix.extract(vendorLibs);
mix.js('resources/js/app.js', 'public/js/')
    .mix.scripts([
        'resources/js/jquery.min.js',
        'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
        'resources/js/reveal.min.js',
        'resources/js/jquery-ui.min.js',
        // 'resources/js/jquery.dataTables.min.js',
        // 'resources/js/dataTables.bootstrap.min.js',
        'resources/js/jquery.autocomplete.min.js',
        'resources/js/selectize.min.js',
        'resources/js/js.cookie.min.js',
        'resources/js/rcswitcher.min.js',
        // 'resources/js/jquery.sortable.js'
], 'public/vendor.js')
//    .sourceMaps()
    .mix.sass('resources/scss/app.scss', 'public/css/app.css')
    .mix.sass('resources/scss/overrides.scss', 'public/css/overrides.css')
    .mix.styles([
        'resources/scss/bootstrap.css',
        'resources/scss/jquery-ui.css',
        'resources/scss/normalize.min.css',
        'resources/scss/jquery.ui.datepicker.min.css',
        // 'resources/scss/jquery.dataTables.min.css',
        // 'resources/scss/dataTables.jqueryui.css',
        // 'resources/scss/dataTables.bootstrap.css',
        'resources/scss/selectize.css',
        'resources/scss/selectize.bootstrap3.css',
        'resources/scss/font-awesome.css',
        'node_modules/bootstrap-block-grid/dist/bootstrap3-block-grid.min.css'
], 'public/css/vendor.css');
// mix.standaloneSass('src', output); <-- Faster, but isolated from Webpack.
// mix.fastSass('src', output); <-- Alias for mix.standaloneSass().
// mix.less(src, output);
// mix.stylus(src, output);
// mix.postCss(src, output, [require('postcss-some-plugin')()]);
// mix.browserSync('my-site.dev');
// mix.combine(files, destination);
// mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
// mix.copy(from, to);
// mix.copyDirectory(fromDir, toDir);
// mix.minify(file);
// mix.sourceMaps(); // Enable sourcemaps
// mix.version(); // Enable versioning.
// mix.disableNotifications();
// mix.setPublicPath('path/to/public');
// mix.setResourceRoot('prefix/for/resource/locators');
// mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
// mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
// mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
// mix.options({
//   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
//   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
//   purifyCss: false, // Remove unused CSS selectors.
//   uglify: {}, // Uglify-specific options. https://webpack.github.io/docs/list-of-plugins.html#uglifyjsplugin
//   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
// });
