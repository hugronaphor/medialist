var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var plumber = require('gulp-plumber');
var notify = require('gulp-notify');
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');
var gutil = require('gulp-util');
var gulpif = require('gulp-if');
var stripDebug = require('gulp-strip-debug');
var concat = require('gulp-concat');
var filter = require('gulp-filter');
var browserSync = require('browser-sync').create();
var autoprefixer = require('gulp-autoprefixer');
var babel = require('gulp-babel');
var chmod = require('gulp-chmod');

// Set some defaults
var isDev = true;
var isProd = false;

// If "prod" is passed from the command line then update the defaults
if (gutil.env._[0] === 'prod') {
  isDev = false;
  isProd = true;
}

location = './web/themes/custom/medialist';
destination = './web/themes/custom/medialist';
jsFileName = 'script.min.js';

var plumberErrorHandler = {
  errorHandler: notify.onError({
    title: 'Gulp',
    message: 'Error: <%= error.message %>'
  })
};


// Gulp Sass Task
gulp.task('sass', function () {
  return gulp
    .src([
      location + '/sass/*.{scss,sass}'
    ])
    .pipe(plumber(plumberErrorHandler))
    .pipe(sourcemaps.init())
    .pipe(
      sass({
        includePaths: [
          'node_modules/bootstrap/scss/',
          'node_modules/breakpoint-sass/stylesheets',
          location + '/sass/theme'
        ]
      })
    )
    .pipe(
      autoprefixer({
        browsers: ['defaults', 'ie >= 11', 'Safari >= 10', 'iOS >= 9']
      })
    )
    .pipe(gulpif(isProd, cleanCSS()))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(destination + '/css'))
    .pipe(filter('**/*.css')) // Filtering stream to only css files
    .pipe(browserSync.reload({stream: true}));
});

gulp.task('scripts', function () {
  return gulp
    .src([
      'node_modules/@babel/polyfill/dist/polyfill.js',
      'node_modules/custom-event-polyfill/polyfill.js',
      // 'node_modules/jquery/dist/jquery.min.js',
      // 'node_modules/bootstrap/js/dist/*.js',
      location + '/js/lib/*.js',
      location + '/js/src/*.js',
    ])
    .pipe(plumber(plumberErrorHandler))
    .pipe(
      babel({
        presets: ['@babel/preset-env']
      })
    )
    .pipe(concat(jsFileName))
    .pipe(gulpif(isProd, stripDebug()))
    .pipe(gulpif(isProd, uglify()))
    .pipe(chmod(0o755))
    .pipe(gulp.dest(destination + '/js/'))
    .pipe(
      notify({
        message: 'Scripts task complete: combined, debug stripped, uglified'
      })
    );
});

gulp.task('default',
  gulp.parallel(
    'sass',
    'scripts',
  ),
  function () {
    gulp.watch(location + '/sass/**/*.scss', gulp.series('sass'));
    gulp.watch(location + '/js/src/**/*.js', gulp.series('jshint'));
    gulp.watch(location + '/js/src/*.js', gulp.series('scripts'));
    gulp.watch(location + '/css/*.css', gulp.series('sizereport'));
    gulp.watch(location + '/js/libraries/*.js', gulp.series('libraries'));
  }
);