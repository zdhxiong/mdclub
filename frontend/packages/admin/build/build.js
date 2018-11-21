'use strict';

process.env.NODE_ENV = 'production';

const ora = require('ora');
const rm = require('rimraf');
const path = require('path');
const copy = require('recursive-copy');
const chalk = require('chalk');
const webpack = require('webpack');
const webpackConfig = require('./webpack.prod.conf');
const buildConfig = require('./config');

const spinner = ora('building for production...');
spinner.start();

rm(path.resolve(__dirname, '../dist'), err => {
  if (err) throw err;

  webpack(webpackConfig, (err, stats) => {
    spinner.stop();
    if (err) throw err;

    process.stdout.write(stats.toString({
      colors: true,
      modules: false,
      children: false,
      chunks: false,
      chunkModules: false
    }) + '\n\n');

    if (stats.hasErrors()) {
      console.log(chalk.red('  Build failed with errors.\n'));
      process.exit(1);
    }

    // 复制生成的生产环境文件到 mdclub 项目中
    rm(buildConfig.targetFolder, err => {
      if (err) throw err;

      copy(path.resolve(__dirname, '../dist'), buildConfig.targetFolder, err => {
        if (err) throw err;

        console.log(chalk.cyan('  Build complete.\n'));
      });
    });
  });
});
