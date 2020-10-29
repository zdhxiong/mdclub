const FileSystem = require('fs');
const webpack = require('webpack');
const { merge } = require('webpack-merge');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const commonConfig  = require('./webpack.common.js');
const { outputFolder, resolve } = require('./config');

const prodConfig = merge(commonConfig, {
  mode: 'production',
  devtool: 'source-map',
  optimization: {
    minimize: true,
    minimizer: [new TerserJSPlugin({}), new OptimizeCSSAssetsPlugin({})],
  },
  output: {
    filename: 'index.[contenthash:8].js',
    path: outputFolder,
    publicPath: './',
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: 'production',
      },
    }),
    new CleanWebpackPlugin(),
    new MiniCssExtractPlugin({
      filename: 'index.[contenthash:8].css',
    }),
    // 替换 HTML 中的 JS、CSS 文件名
    function() {
      const phpFiles = [
        resolve('./src/admin.php'),
      ];

      this.hooks.done.tap('replaceHash', statsData => {
        let stats = statsData.toJson();

        if (stats.errors.length) {
          return;
        }

        const fileNames = stats.assetsByChunkName.main;

        [
          [/(index\..*?\.js)/, fileNames[1]],
          [/(index\..*?\.css)/, fileNames[0]],
          ["$NODE_ENV = 'development'", "$NODE_ENV = 'production'"],
        ].forEach(arr => {
          phpFiles.forEach(phpFile => {
            let htmlOutput = FileSystem.readFileSync(phpFile, 'utf-8').replace(arr[0], arr[1]);
            FileSystem.writeFileSync(phpFile, htmlOutput);
          });
        });

        FileSystem.copyFileSync(resolve('./src/admin.php'), resolve('../mdclub/templates/default/admin.php'));
      });
    },
  ],
});

if (process.env.npm_config_report) {
  const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
  prodConfig.plugins.push(new BundleAnalyzerPlugin());
}

module.exports = prodConfig;
