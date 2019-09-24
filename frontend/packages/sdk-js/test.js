const rollup = require('rollup');
const { eslint } = require('rollup-plugin-eslint');
const typescript = require('rollup-plugin-typescript');
const json = require('rollup-plugin-json');
const resolve = require('rollup-plugin-node-resolve');
const commonjs = require('rollup-plugin-commonjs');
const serverFactory = require('spa-server');

async function test() {
  await rollup.watch({
    input: './test/index.ts',
    output: [{
      strict: true,
      name: 'mdclubTest',
      format: 'umd',
      file: './test/dist.js',
    }],
    plugins: [
      resolve(),
      commonjs(),
      json(),
      eslint({
        fix: true,
      }),
      typescript(),
    ],
    watch: {
      include: './test/unit/**/*'
    }
  });

  const server = serverFactory.create({
    path: './',
  });

  server.start();

  console.log('打开 http://127.0.0.1:8888/test/index.html 开始测试');
}

test();
