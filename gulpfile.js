const gulp = require('gulp');
const gutil = require('gulp-util');
const readlineSync = require('readline-sync');
const rename = require('gulp-rename');
const replace = require('gulp-replace');
const insertLines = require('gulp-insert-lines');
const sass = require('gulp-sass')(require('sass'));
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const imagemin = require('gulp-imagemin');
const imageminPngquant = require('imagemin-jpeg-recompress');
const imageminJpegRecompress = require('imagemin-jpeg-recompress');
const autoprefixer = require('gulp-autoprefixer');
const browserSync = require('browser-sync').create();

gulp.task('install', function(done) {

    gulp.src('node_modules/bootstrap/scss/_variables.scss')
        .pipe(rename('_variables-reference.scss'))
        .pipe(gulp.dest('scss'));

    gulp.src(['node_modules/bootstrap/scss/bootstrap.scss'])
        .pipe(replace('@import "', '@import "../node_modules/bootstrap/scss/'))
        .pipe(insertLines({
            'before': '@import "../node_modules/bootstrap/scss/variables";',
            'lineBefore': '@import "variables";'
        }))
        .pipe(rename('_bootstrap.scss'))
        .pipe(gulp.dest('scss'));

    // Prompt User for a theme name
    var theme_name = readlineSync.question('Please enter a name for your theme (Short)');
    theme_name = theme_name.replace(/\s/g, '-').toLowerCase()
    console.log('Renaming theme strings and files with ' + theme_name);

    gulp.src(['./**/*.php','./**/*.js','./**/*.css','!node_modules/**','!gulpfile.js'], {base: './'})
        .pipe(replace( "'_s'", "'" + theme_name + "'" ))
        .pipe(replace( "_s_", theme_name + "_" ))
        .pipe(replace( " _s", " " + theme_name ))
        .pipe(replace( "_s-", theme_name + "-" ))
        .pipe(replace( "_s/block-filters", theme_name + "/block-filters" ))
        .pipe(gulp.dest('./'));

    gulp.src("languages/_s.pot")
        .pipe(rename(theme_name + ".pot"))
        .pipe(gulp.dest("languages"));

    done();
});

gulp.task('sass', function(done) {
    gulp.src('scss/**/*.scss')
        .pipe(sass({outputStyle: 'expanded'}))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .on('error', gutil.log)
        .pipe(gulp.dest('css'))
        .pipe(browserSync.reload({stream:true}));

    gulp.src('scss/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(sourcemaps.init())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('assets/css'))
        .pipe(browserSync.reload({stream:true}))

    done();
});

gulp.task('js', function(done) {
    gulp.src([
        'node_modules/lazysizes/**/*.min.js',
        'js/*.js',
        '!js/gutenberg.js',
    ])
        .pipe(uglify({compress: { unused: false } }))
        .on('error', gutil.log)
        .pipe(gulp.dest('assets/js'))
        .pipe(browserSync.reload({stream:true}))

    done();
});

gulp.task('php', function(done) {
    gulp.src('**/*.php')
        .pipe(browserSync.reload({stream:true}))

    done();
});

gulp.task('imagesreduced', function () {
    return gulp.src('images/**/*.{png,jpeg,jpg,svg,gif}')
        .pipe(imagemin())
        .pipe(gulp.dest('assets/images'));
});

gulp.task('imagescompress', function () {
    return gulp.src('images/**/*.{png,jpeg,jpg,svg,gif}')
        .pipe(imagemin([ //override the default by setting our own
            //because we overrode, we want to call all the defaults that were called behind the scene
            imagemin.optipng(), //call default for imagemin
            imagemin.svgo(), //call default for imagemin
            imagemin.gifsicle(),//call default for imagemin
            imagemin.jpegtran(),//call default for imagemin
            imageminPngquant(), //call the lossy compression for PNG
            imageminJpegRecompress()//call the lossy compression for JPEG/JPG
        ]))
        .pipe(gulp.dest('assets/images'));
});

// Static server
/*
gulp.task('browser-sync', function() {
    browserSync.init({
        server: {
            baseDir: "./"
        }
    });
});
*/

// Proxy
gulp.task('browser-sync', function(done) {
    browserSync.init({
        proxy: "http://localdomain.test"
    });
    done();
});


gulp.task('watch', function(done) {
    gulp.watch('js/*.js', gulp.parallel('js'));
    gulp.watch('scss/**/*.scss', gulp.parallel('sass'));
    gulp.watch('**/*.php', gulp.parallel('php'));
    gulp.watch('images/**/*.{png,jpeg,jpg,svg,gif}', gulp.parallel('imagesreduced'));

    done();
});

gulp.task('default', gulp.parallel(['js', 'sass', 'imagesreduced', 'browser-sync', 'watch']));
