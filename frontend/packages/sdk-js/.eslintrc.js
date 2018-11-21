// https://eslint.org/docs/user-guide/configuring

module.exports = {
  env: {
    browser: true,
    es6: true,
  },
  extends: 'airbnb-base',
  plugins: [
    "import",
  ],
  rules: {
    'func-names': 0,
    'no-param-reassign': 0,
    'camelcase': 0
  }
};
