const rollup = require('rollup');
const { eslint } = require('rollup-plugin-eslint');
const { uglify } = require('rollup-plugin-uglify');
const buble = require('@rollup/plugin-buble');
const typescript = require('@rollup/plugin-typescript');
const polyfill = require('rollup-plugin-polyfill');
const resolve = require('@rollup/plugin-node-resolve').default;
const commonjs = require('@rollup/plugin-commonjs');
const json = require('@rollup/plugin-json');
const tsconfig = require('./src/tsconfig.json');
const pkg = require('./package.json');
const serverFactory = require('spa-server');

const args = process.argv.splice(2);

delete tsconfig.compilerOptions.declaration;
delete tsconfig.compilerOptions.declarationDir;
delete tsconfig.compilerOptions.outDir;

const banner = `
/*!
 * mdclub-sdk ${pkg.version} (${pkg.homepage})
 * Copyright 2018-${new Date().getFullYear()} ${pkg.author}
 * Licensed under ${pkg.license}
 */
`.trim();

const input = './src/index.ts';

const plugins = [
  resolve(),
  commonjs(),
  typescript(Object.assign(
    tsconfig.compilerOptions,
    { include: './src/**/*' }
  )),
];

const outputOptions = {
  strict: true,
  name: 'mdclubSDK',
  banner,
};

// 编译成 ES6 模块化文件
async function buildEsm() {
  const bundle = await rollup.rollup({ input, plugins });

  await bundle.write(Object.assign({}, outputOptions, {
    sourcemap: true,
    format: 'es',
    file: './dist/mdclub-sdk.esm.js',
  }));
}

// 编译成 umd 文件
async function buildUmd() {
  plugins.push(
    buble(),
    polyfill([
      'mdn-polyfills/MouseEvent',
      'mdn-polyfills/CustomEvent',
      'promise-polyfill/src/polyfill',
    ]),
  );

  const bundle = await rollup.rollup({ input, plugins });

  await bundle.write(Object.assign({}, outputOptions, {
    sourcemap: true,
    format: 'umd',
    file: './dist/mdclub-sdk.js',
  }));
}

// 编译成 umd 文件，并压缩
async function buildUmdUglify() {
  plugins.push(
    uglify({
      output: {
        preamble: banner,
      }
    })
  );

  const bundle = await rollup.rollup({ input, plugins });

  await bundle.write(Object.assign({}, outputOptions, {
    sourcemap: true,
    format: 'umd',
    file: './dist/mdclub-sdk.min.js',
  }));
}

async function build() {
  await buildEsm();
  await buildUmd();
  await buildUmdUglify();
}

async function test() {
  const bundle = await rollup.rollup({
    input: './test/index.ts',
    plugins: [
      resolve({ mainFields: ["jsnext", "preferBuiltins", "browser"] }),
      commonjs(),
      json(),
      eslint({
        fix: true,
      }),
      typescript({
        include: './test/**/*',
        module: "ES6",
        target: "ES6"
      }),
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
    name: 'MDClubSDKTest',
    format: 'umd',
    file: './test/dist.js',
  });

  const server = serverFactory.create({
    path: './',
    port: 8889
  });

  server.start();

  console.log('打开 http://127.0.0.1:8889/test/index.html 开始测试');
}

if (args.indexOf('--build') > -1) {
  build().catch(e => console.log(e));
} else if (args.indexOf('--test') > -1) {
  test();
}
