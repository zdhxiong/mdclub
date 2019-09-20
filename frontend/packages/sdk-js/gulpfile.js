const gulp = require('gulp');
const header = require('gulp-header');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const rollup = require('rollup');
const buble = require('rollup-plugin-buble');
const { eslint } = require('rollup-plugin-eslint');
const typescript = require('rollup-plugin-typescript');
const polyfill = require('rollup-plugin-polyfill');
const resolve = require('rollup-plugin-node-resolve');
const json = require('rollup-plugin-json');
const commonjs = require('rollup-plugin-commonjs');
const serverFactory = require('spa-server');
const pkg = require('./package.json');

const banner = `
/*!
 * mdclub-sdk ${pkg.version} (${pkg.homepage})
 * Copyright 2018-${new Date().getFullYear()} ${pkg.author}
 * Licensed under ${pkg.license}
 */
`.trim();

async function umd() {
  const bundle = await rollup.rollup({
    input: './src/index.ts',
    plugins: [
      resolve(),
      commonjs(),
      eslint({
        fix: true,
      }),
      typescript(),
      buble(),
      polyfill([
        'mdn-polyfills/MouseEvent',
        'mdn-polyfills/CustomEvent',
        'promise-polyfill/src/polyfill',
      ]),
    ],
  });

  await bundle.write({
    strict: true,
    name: 'mdclubSDK',
    format: 'umd',
    file: './dist/mdclub-sdk.js',
    banner,
  });

  await gulp.src('./dist/mdclub-sdk.js')
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(header(banner))
    .pipe(rename('mdclub-sdk.min.js'))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./dist/'));
}

async function esm() {
  const bundle = await rollup.rollup({
    input: './src/index.ts',
    plugins: [
      resolve(),
      commonjs(),
      eslint({
        fix: true,
      }),
      typescript(),
    ],
  });

  await bundle.write({
    strict: true,
    name: 'mdclubSDK',
    format: 'es',
    file: './dist/mdclub-sdk.esm.js',
    banner,
  });
}

async function test() {
  const bundle = await rollup.rollup({
    input: './test/index.ts',
    plugins: [
      resolve(),
      commonjs(),
      json(),
      eslint({
        fix: true,
      }),
      typescript(),
    ],
  });

  await bundle.write({
    strict: true,
    name: 'mdclubTest',
    format: 'es',
    file: './test/dist.js',
  });

  const server = serverFactory.create({
    path: './',
  });

  server.start();

  console.log('打开 http://127.0.0.1:8888/test/index.html 开始测试');
}

gulp.task('build', gulp.parallel(umd, esm));

gulp.task('test', test);
