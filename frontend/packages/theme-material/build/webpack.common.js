const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const postcssImport = require('postcss-import');
const autoprefixer = require('autoprefixer');
const { outputFolder, resolve, isProduction } = require('./config');

function styleLoaders(loader) {
  const loaders = [
    {
      loader: 'css-loader',
      options: {
        importLoaders: 1,
        sourceMap: true,
      },
    },
    {
      loader: 'postcss-loader',
      options: {
        postcssOptions: {
          plugins: [
            postcssImport(),
            autoprefixer(),
          ],
          sourceMap: true,
        },
      }
    },
  ];

  if (loader) {
    loaders.push({
      loader: `${loader}-loader`,
      options: {
        sourceMap: true,
      },
    });
  }

  if (isProduction) {
    return [{
      loader: MiniCssExtractPlugin.loader,
      options: {
        esModule: true,
        publicPath: outputFolder,
      },
    },].concat(loaders);
  } else {
    return ['style-loader'].concat(loaders);
  }
}

module.exports = {
  node: false,
  entry: './src/main.js',
  resolve: {
    extensions: ['.js'],
    alias: {
      '~': resolve('./src/'),
      'mdui.jq$': 'mdui.jq/es/$.js',
      mdui$: 'mdui/es/mdui.js',
    },
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        use: [
          {
            loader: 'buble-loader',
          },
          'babel-loader',
        ],
        include: [
          resolve('./src'),
          resolve('./node_modules/mdui/es'),
          resolve('./node_modules/mdui.jq/es'),
          resolve('./node_modules/mdui.editor/es'),
          resolve('./node_modules/mdclub-sdk-js/es'),
        ],
      },
      {
        test: /\.css$/,
        use: styleLoaders()
      },
      {
        test: /\.less$/,
        use: styleLoaders('less')
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf|png|svg|jpg|gif)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              publicPath: isProduction ? './' : './static/theme/material/',
            },
          },
        ],
      },
    ],
  },
};
