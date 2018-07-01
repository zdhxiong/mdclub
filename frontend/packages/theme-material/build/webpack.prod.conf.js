'use strict';
const path = require('path');
const FileSystem = require('fs');
const webpack = require('webpack');
const merge = require('webpack-merge');
const baseWebpackConfig = require('./webpack.base.conf');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

const webpackConfig = merge(baseWebpackConfig, {
  devtool: '#source-map',
  output: {
    path: path.resolve(__dirname, '../dist'),
    filename: 'js/[name].[chunkhash].js',
    chunkFilename: 'js/[id].[chunkhash].js'
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: 'production'
      }
    }),
    new UglifyJsPlugin({
      uglifyOptions: {
        compress: {
          warnings: false
        }
      },
      sourceMap: true,
      parallel: true
    }),
    new ExtractTextPlugin({
      filename: 'css/[name].[contenthash].css',
      allChunks: false,
    }),
    new OptimizeCSSPlugin({
      cssProcessorOptions: {
        safe: true,
        map: { inline: false }
      }
    }),
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
            [/(js\/vendor.*?\.js)/,   stats.assetsByChunkName.vendor[0]],
            [/(js\/app.*?\.js)/,      stats.assetsByChunkName.app[0]],
            [/(js\/manifest.*?\.js)/, stats.assetsByChunkName.manifest[0]],
            [/(css\/vendor.*?\.css)/, stats.assetsByChunkName.vendor[1]],
            [/(css\/app.*?\.css)/,    stats.assetsByChunkName.app[1]],
            [/'production';\/\/ /g,   `'production'; `],
            [/'development'; /g,      `'development';// `]
          ].forEach(function(arr) {
            files.forEach(function(file) {
              let htmlOutput = FileSystem.readFileSync(file, 'utf-8').replace(arr[0], arr[1]);
              FileSystem.writeFileSync(file, htmlOutput);
            });
          });
        }
      });
    },
    // keep module.id stable when vender modules does not change
    new webpack.HashedModuleIdsPlugin(),
    // enable scope hoisting
    new webpack.optimize.ModuleConcatenationPlugin(),
    // split vendor js into its own file
    new webpack.optimize.CommonsChunkPlugin({
      name: 'vendor',
      minChunks (module) {
        // any required modules inside node_modules are extracted to vendor
        return (
          module.resource &&
          /\.js$/.test(module.resource) &&
          module.resource.indexOf(
            path.join(__dirname, '../node_modules')
          ) === 0 ||
          module.resource.indexOf(
            path.join(__dirname, '../vendor')
          ) === 0
        )
      }
    }),
    // extract webpack runtime and module manifest to its own file in order to
    // prevent vendor hash from being updated whenever app bundle is updated
    new webpack.optimize.CommonsChunkPlugin({
      name: 'manifest',
      minChunks: Infinity
    }),
    // This instance extracts shared chunks from code splitted chunks and bundles them
    // in a separate chunk, similar to the vendor chunk
    // see: https://webpack.js.org/plugins/commons-chunk-plugin/#extra-async-commons-chunk
    new webpack.optimize.CommonsChunkPlugin({
      name: 'app',
      async: 'vendor-async',
      children: true,
      minChunks: 3
    }),

    // copy custom static assets
    new CopyWebpackPlugin([
      {
        from: path.resolve(__dirname, '../static'),
        to: path.resolve(__dirname, '../dist'),
        ignore: ['.*']
      }
    ])
  ]
});

if (process.env.npm_config_report) {
  const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
  webpackConfig.plugins.push(new BundleAnalyzerPlugin())
}

module.exports = webpackConfig;
