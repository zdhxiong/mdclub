// https://eslint.org/docs/user-guide/configuring

module.exports = {
  env: {
    "browser": true
  },
  parser: 'babel-eslint',
  extends: 'airbnb-base',
  rules: {
    // don't require .js extension when importing
    'import/extensions': ['error', 'always', {
      js: 'never',
    }],

    'no-new': 0,
    'func-names': 0,
    'camelcase': 0,
    'no-unused-vars': 0,
    'no-underscore-dangle': 0,
  }
};
