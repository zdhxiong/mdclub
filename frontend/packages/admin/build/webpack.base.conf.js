'use strict';

const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const isProduction = process.env.NODE_ENV === 'production';

function resolve (dir) {
  return path.join(__dirname, '..', dir);
}

function styleLoaders (loader) {
  const loaders = [
    {
      loader: 'css-loader',
      options: {
        sourceMap: isProduction
      }
    },
    {
      loader: 'postcss-loader',
      options: {
        sourceMap: isProduction
      }
    }
  ];

  if (loader) {
    loaders.push({
      loader: loader + '-loader',
      options: {
        sourceMap: isProduction
      }
    });
  }

  if (isProduction) {
    return ExtractTextPlugin.extract({
      use: loaders,
    });
  } else {
    return ['style-loader'].concat(loaders);
  }
}

module.exports = {
  context: resolve('./'),
  entry: {
    app: './src/main.js'
  },
  resolve: {
    extensions: ['.js'],
    alias: {
      '@': resolve('src'),
    }
  },
  module: {
    rules: [
      {
        test: /\.(js)$/,
        loader: 'eslint-loader',
        enforce: 'pre',
        include: [resolve('src')],
        options: {
          formatter: require('eslint-friendly-formatter'),
          emitWarning: true
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        include: [resolve('src')]
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 3000,
          name: 'img/[name].[hash:7].[ext]'
        }
      },
      {
        test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 3000,
          name: 'media/[name].[hash:7].[ext]'
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 3000,
          name: 'fonts/[name].[hash:7].[ext]'
        }
      },
      {
        test: /\.css$/,
        use: styleLoaders()
      },
      {
        test: /\.less$/,
        use: styleLoaders('less')
      }
    ]
  }
};
