module.exports = {
  /*presets: [
    [
      '@babel/preset-env',
      {
        debug: true,
        modules: false,
        loose: true,
        corejs: 3,
        useBuiltIns: 'usage',
        bugfixes: true,
      },
    ],
  ],*/
  plugins: [
    ['@babel/transform-react-jsx', { pragma: 'h' }],
    'jsx-control-statements',
  ],
};
