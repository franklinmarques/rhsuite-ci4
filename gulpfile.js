const {series, src, dest} = require('gulp');
const importCss = require('gulp-import-css');
// const uglify = require('gulp-uglify');
// const rename = require('gulp-rename');
// const cssnano = require('gulp-cssnano');
const minify = require('gulp-minify');
const sourcemaps = require('gulp-sourcemaps');
const concat = require('gulp-concat');
const through2 = require('through2');
const touch = () => through2.obj(function (file, enc, cb) {
    if (file.stat) {
        file.stat.atime = file.stat.mtime = file.stat.ctime = new Date();
    }
    cb(null, file);
});

function makeCompiledCss(cb) {
    src([
        'node_modules/bootstrap/dist/css/bootstrap.css',
        'node_modules/@fortawesome/fontawesome-free/css/all.css',
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
        'node_modules/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
        'node_modules/datatables.net-rowgroup-bs5/css/rowGroup.bootstrap5.min.css',
        'node_modules/datatables.net-rowreorder-bs5/css/rowReorder.bootstrap5.min.css',
        'node_modules/datatables.net-scroller-bs5/css/scroller.bootstrap5.min.css',
        'node_modules/datatables.net-searchpanes-bs5/css/searchPanes.bootstrap5.min.css',

        'assets/css/bucket-ico-fonts.css',
        'assets/css/style.css',
        'assets/css/style-responsive.css',
        'assets/css/custom.css',
    ])
        // .pipe(cssnano({noSource: true}))
        .pipe(sourcemaps.init())
        .pipe(concat('compiled.css'))
        .pipe(importCss())
        // .pipe(dest('public/'))
        // .pipe(rename('uglify.css'))
        // .pipe(uglify())
        .pipe(sourcemaps.write('./_maps'))
        .pipe(touch())
        .pipe(dest('public/'));
    cb();
}

function makeCompiledJs(cb) {
    src([
        'node_modules/jquery/dist/jquery.js',
        // 'node_modules/@popperjs/core/dist/umd/popper.js',
        'node_modules/popper.js/dist/umd/popper.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/@fortawesome/fontawesome-free/js/all.js',
        'node_modules/bootbox/dist/bootbox.min.js',
        'node_modules/bootbox/dist/bootbox.locales.min.js',
        'node_modules/datatables.net/js/jquery.dataTables.min.js',
        'node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
        'node_modules/datatables.net-buttons/js/dataTables.buttons.min.js',
        'node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js',
        'node_modules/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js',
        'node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
        'node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
        'node_modules/datatables.net-rowgroup-bs5/js/rowGroup.bootstrap5.min.js',
        'node_modules/datatables.net-rowreorder-bs5/js/rowReorder.bootstrap5.min.js',
        'node_modules/datatables.net-scroller-bs5/js/scroller.bootstrap5.min.js',
        'node_modules/datatables.net-searchpanes/js/dataTables.searchPanes.min.js',
        'node_modules/datatables.net-searchpanes-bs5/js/searchPanes.bootstrap5.min.js',
        'node_modules/jquery.nicescroll/dist/jquery.nicescroll.js',
        'node_modules/jquery-slimscroll/jquery.slimscroll.js',
        'node_modules/jquery.scrollTo/jquery.scrollTo.js',
        'node_modules/dcjqaccordion/js/jquery.cookie.js',
        'node_modules/dcjqaccordion/js/jquery.hoverIntent.minified.js',
        'node_modules/dcjqaccordion/js/jquery.dcjqaccordion.2.7.js',
        // 'public/assets/js/jquery.dcjqaccordion.2.9.js',

        'assets/js/ajax/ajax.custom.js',
        'assets/js/ajax/ajax.form.js',
        'assets/js/ajax/ajax.simple.js',
        'assets/js/ajax/ajax.upload.js',
        'assets/js/scripts.js',
    ])
        .pipe(sourcemaps.init())
        .pipe(minify({noSource: true}))
        .pipe(concat('compiled.js'))
        // .pipe(dest('public/'))
        // .pipe(rename('uglify.js'))
        // .pipe(uglify())
        .pipe(sourcemaps.write('./_maps'))
        .pipe(touch())
        .pipe(dest('public/'));
    cb();
}

exports.default = series(makeCompiledCss, makeCompiledJs);
