const FileSystem = require('fs');
const webpack = require('webpack');
const { merge } = require('webpack-merge');
const commonConfig = require('./webpack.common.js');
const { outputFolder, resolve } = require('./config');

const devConfig = merge(commonConfig, {
  mode: 'development',
  devtool: 'inline-source-map',
  devServer: {
    clientLogLevel: 'error',
    filename: 'index.js',
    hot: true,
    compress: true,
    host: 'localhost',
    port: 8081,
    open: false,
    inline: true,
    progress: true,
    disableHostCheck: true,
    contentBase: outputFolder,
  },
  output: {
    filename: 'index.js',
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: 'development',
      },
    }),
    // 替换 HTML 中的环境变量
    function() {
      const phpFiles = [
        resolve('./src/admin.php'),
      ];

      this.hooks.done.tap('replaceEnvironment', () => {
        phpFiles.forEach(phpFile => {
          let htmlOutput = FileSystem
            .readFileSync(phpFile, 'utf-8')
            .replace("$NODE_ENV = 'production'", "$NODE_ENV = 'development'");

          FileSystem.writeFileSync(phpFile, htmlOutput);
        });

        FileSystem.copyFileSync(resolve('./src/admin.php'), resolve('../mdclub/templates/default/admin.php'));
      });
    },
  ],
});

module.exports = devConfig;
