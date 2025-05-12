const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const { PurgeCSS } = require('purgecss');
const through2 = require('through2');

function compileSass() {
  return gulp.src('sass/main.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    // // Use PurgeCSS to remove unused CSS.
    // .pipe(through2.obj(async function(file, enc, cb) {
    //   if (file.isNull()) {
    //     cb(null, file);
    //     return;
    //   }

    //   // Try to purge CSS.
    //   try {
    //     const purgeCSSResult = await new PurgeCSS().purge({
    //       content: ['views/**/*.twig'],
    //       css: [{raw: file.contents.toString()}],
    //       defaultExtractor: content => {
    //         // Only extract alphanumeric characters, hyphens, underscores, colons and slashes.
    //         return content.match(/[A-Za-z0-9-_:/]+/g) || [];
    //       }
    //     });

    //     if (purgeCSSResult.length > 0) {
    //       file.contents = Buffer.from(purgeCSSResult[0].css);
    //     }

    //     cb(null, file);
    //   } catch (err) {
    //     console.error('PurgeCSS error:', err);
    //     cb(err);
    //   }
    // }))
    .pipe(cleanCSS())
    .pipe(rename('styles.css'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('public/dist/css'));
}

gulp.task('sass', compileSass);
gulp.task('compile:sass', compileSass);
gulp.task('default', gulp.series('compile:sass'));
