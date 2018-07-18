const gulp = require('gulp');
const header = require('gulp-header');
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const rename = require('gulp-rename');
const rollup = require('rollup-stream');
const resolve = require('rollup-plugin-node-resolve');
const buble = require('rollup-plugin-buble');
const commonjs = require('rollup-plugin-commonjs');
const eslint = require('rollup-plugin-eslint');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const pkg = require('./package.json');

const banner = `
/**
 * mdclub-sdk-js ${pkg.version} (${pkg.homepage})
 * Copyright 2018-${new Date().getFullYear()} ${pkg.author}
 * Licensed under ${pkg.license}
 */
`.trim();

gulp.task('build', (cb) => {
  rollup({
    input: './src/index.js',
    plugins: [
      resolve(),
      commonjs(),
      eslint(),
      buble(),
    ],
    format: 'umd',
    name: 'mdclubSDK',
    banner,
  })
    .pipe(source('index.js', './src'))
    .pipe(buffer())
    .pipe(rename('mdclub-sdk.js'))
    .pipe(gulp.dest('./dist/'))
    .on('end', () => {
      gulp.src('./dist/mdclub-sdk.js')
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(header(banner))
        .pipe(rename('mdclub-sdk.min.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dist/'))
        .on('end', () => {
          if (cb) {
            cb();
          }
        });
    });
});
