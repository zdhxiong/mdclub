const path = require('path');

module.exports = {
  outputFolder: path.resolve(__dirname, '../../mdclub/public/static/admin'),
  resolve: dir => path.resolve(__dirname, '..', dir),
  isProduction: process.env.NODE_ENV === 'production',
  isDevelopment: process.env.NODE_ENV === 'development',
}
