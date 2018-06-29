import commonjs from 'rollup-plugin-commonjs';
import eslint from 'rollup-plugin-eslint';
import resolve from 'rollup-plugin-node-resolve';
import babel from 'rollup-plugin-babel';
import { uglify } from 'rollup-plugin-uglify';

export default {
  input: 'src/index.js',
  output: {
    file: 'dist/index.js',
    format: 'umd',
    name: 'mdclubAPI',
    sourcemap: true,
  },
  plugins: [
    resolve(),
    commonjs(),
    eslint(),
    babel({
      exclude: 'node_modules/**'
    }),
    uglify(),
  ],
};
