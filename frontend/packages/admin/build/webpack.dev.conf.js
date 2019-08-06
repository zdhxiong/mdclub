'use strict';

const webpack = require('webpack');
const merge = require('webpack-merge');
const baseWebpackConfig = require('./webpack.base.conf');
const FriendlyErrorsPlugin = require('friendly-errors-webpack-plugin');
const portfinder = require('portfinder');
const AssetsPlugin = require('assets-webpack-plugin');
const packageConfig = require('../package.json');
const buildConfig = require('./config');

const devWebpackConfig = merge(baseWebpackConfig, {
  // cheap-module-eval-source-map is faster for development
  devtool: 'eval-source-map',

  devServer: {
    clientLogLevel: 'warning',
    hot: true,
    compress: true,
    host: 'localhost',
    port: 8080,
    open: false,
    overlay:  { warnings: false, errors: true },
    publicPath: '/',
    proxy: {},
    headers: {
      "Access-Control-Allow-Origin": "*",
    },
    quiet: true, // necessary for FriendlyErrorsPlugin
    watchOptions: {
      poll: false,
    },
    disableHostCheck: true,
  },

  output: {
    publicPath: 'http://localhost:8080/'
  },

  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: 'development'
      }
    }),
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NamedModulesPlugin(), // HMR shows correct file names in console on update.
    new webpack.NoEmitOnErrorsPlugin(),

    // 生成包含文件信息的 json 文件到 mdclub 项目
    new AssetsPlugin({
      fullPath: false, // 不含文件路径
      path: buildConfig.targetFolder, // 生成的 json 文件存储路径
      prettyPrint: true, // 格式化生成的 json
      includeAllFileTypes: false,
      fileTypes: ['js', 'css'], // 仅包含 js 和 css 文件
    }),
  ],
});

module.exports = new Promise((resolve, reject) => {
  portfinder.basePort = 8080;
  portfinder.getPort((err, port) => {
    if (err) {
      reject(err);
    } else {
      // add port to devServer config
      devWebpackConfig.devServer.port = port;

      // Add FriendlyErrorsPlugin
      devWebpackConfig.plugins.push(new FriendlyErrorsPlugin({
        compilationSuccessInfo: {
          messages: [`Your application is running here: http://${devWebpackConfig.devServer.host}:${port}`],
        },
        onErrors: function() {
          const notifier = require('node-notifier');

          return (severity, errors) => {
            if (severity !== 'error') {
              return;
            }

            const error = errors[0];
            const filename = error.file && error.file.split('!').pop();

            notifier.notify({
              title: packageConfig.name,
              message: severity + ': ' + error.name,
              subtitle: filename || '',
            });
          };
        }(),
      }));

      resolve(devWebpackConfig);
    }
  })
});
