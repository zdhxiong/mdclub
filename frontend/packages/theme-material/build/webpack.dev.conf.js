'use strict';
const path = require('path');
const FileSystem = require('fs');
const webpack = require('webpack');
const merge = require('webpack-merge');
const baseWebpackConfig = require('./webpack.base.conf');
const HtmlWebpackPlugin = require('html-webpack-plugin')
const FriendlyErrorsPlugin = require('friendly-errors-webpack-plugin');
const portfinder = require('portfinder');
const packageConfig = require('../package.json');

const devWebpackConfig = merge(baseWebpackConfig, {
  // cheap-module-eval-source-map is faster for development
  devtool: 'eval-source-map',

  // these devServer options should be customized in /config/index.js
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
    quiet: true, // necessary for FriendlyErrorsPlugin
    watchOptions: {
      poll: false,
    }
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
    // https://github.com/ampedandwired/html-webpack-plugin
    new HtmlWebpackPlugin({
      filename: 'index.html',
      template: 'index.html',
      inject: true
    }),
  ]
});

module.exports = new Promise((resolve, reject) => {
  portfinder.basePort = 8080;
  portfinder.getPort((err, port) => {
    if (err) {
      reject(err)
    } else {
      // publish the new Port, necessary for e2e tests
      process.env.PORT = port;
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
            if (severity !== 'error') return;

            const error = errors[0];
            const filename = error.file && error.file.split('!').pop();

            notifier.notify({
              title: packageConfig.name,
              message: severity + ': ' + error.name,
              subtitle: filename || '',
            })
          }
        }()
      }));

      devWebpackConfig.plugins.push(
        // 替换 HTML 中的 JS、CSS
        function() {
          const files = [
            path.resolve(__dirname, '../../../../../application/home/view/default/Public/header.php'),
            path.resolve(__dirname, '../../../../../application/home/view/default/Public/footer.php'),
          ];
          this.plugin('done', function(statsData) {
            let stats = statsData.toJson();

            if (!stats.errors.length) {
              [
                [/http:\/\/localhost:(.*?)\/app\.js/g,  `http://localhost:${port}/app.js`],
                [/'production'; /g,                     `'production';// `],
                [/'development';\/\/ /g,                `'development'; `]
              ].forEach(function(arr) {
                files.forEach(function(file) {
                  let htmlOutput = FileSystem.readFileSync(file, 'utf-8').replace(arr[0], arr[1]);
                  FileSystem.writeFileSync(file, htmlOutput);
                });
              });
            }
          });
        });

      resolve(devWebpackConfig);
    }
  })
});
