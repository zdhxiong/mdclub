const gulp = require('gulp');
const rollup = require('gulp-better-rollup');
const header = require('gulp-header');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const buble = require('rollup-plugin-buble');
const { eslint } = require('rollup-plugin-eslint');
const resolve = require('rollup-plugin-node-resolve');
const commonjs = require('rollup-plugin-commonjs');
const pkg = require('./package.json');


const banner = `
/*!
 * mdclub-sdk-js ${pkg.version} (${pkg.homepage})
 * Copyright 2018-${new Date().getFullYear()} ${pkg.author}
 * Licensed under ${pkg.license}
 */
`.trim();

function compile(cb) {
  gulp.src('./src/index.js')
    .pipe(rollup({
      plugins: [resolve(), commonjs(), eslint(), buble()],
    }, {
      name: 'mdclubSDK',
      format: 'umd',
      file: 'mdclub-sdk.js',
      banner,
    }))
    .pipe(gulp.dest('./dist/'))
    .on('end', cb);
}

function compress(cb) {
  gulp.src('./dist/mdclub-sdk.js')
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(header(banner))
    .pipe(rename('mdclub-sdk.min.js'))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./dist/'))
    .on('end', cb);
}

gulp.task('build', gulp.series(compile, compress));
