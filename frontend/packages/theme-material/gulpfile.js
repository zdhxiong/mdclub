(function () {
  'use strict';

  // 引入 gulp 模块
  const del = require('del');
  const gulp = require('gulp');
  const autoprefixer = require('gulp-autoprefixer');
  const cleanCSS = require('gulp-clean-css');
  const csscomb = require('gulp-csscomb');
  const header = require('gulp-header');
  const jsImport = require('gulp-js-import');
  const less = require('gulp-less');
  const rev = require('gulp-rev');
  const revCollector = require('gulp-rev-collector');
  const sourcemaps = require('gulp-sourcemaps');
  const uglify = require('gulp-uglify');
  const rollup = require('gulp-better-rollup');
  const babel = require('rollup-plugin-babel');
  const runSequence = require('run-sequence');

  const distPath = './dist/';
  const viewPath = '../../../../application/home/view/default/';

  // 插件的配置
  const configs = {
    autoprefixer: {
      browsers: [
        'last 2 versions',
        '> 1%',
        'Chrome >= 30',
        'Firefox >= 30',
        'ie >= 10',
        'Safari >= 8',
      ],
    },
    cleanCSS: {
      compatibility: 'ie10'
    },
    revManifest: {
      base: './',
      merge: true
    },
    del: {
      force: true
    },
    jsImport: {
      hideConsole: true
    }
  };

  /**
   * 更新 html 文件中的 CSS、JS 文件
   */
  gulp.task('_rev', () => {
    return gulp.src([
      './rev-manifest.json',
      viewPath + 'Public/head.php',
      viewPath + 'Public/footer.php'
    ])
      .pipe(revCollector({
        replaceReved: true
      }))
      .pipe(gulp.dest(viewPath + 'Public/'));
  });

  /**
   * 删除 vendor.css 文件
   */
  gulp.task('_del-vendor-css', () => {
    return del([
      distPath + 'css/vendor-*.css',
      distPath + 'css/vendor-*.map'
    ], configs.del);
  });

  /**
   * 删除 main.css 文件
   */
  gulp.task('_del-main-css', () => {
    return del([
      distPath + 'css/main-*.css',
      distPath + 'css/main-*.map'
    ], configs.del);
  });

  /**
   * 删除 vendor.js 文件
   */
  gulp.task('_del-vendor-js', () => {
    return del([
      distPath + 'js/vendor-*.js',
      distPath + 'js/vendor-*.map'
    ], configs.del);
  });

  /**
   * 删除 main.js 文件
   */
  gulp.task('_del-main-js', () => {
    return del([
      distPath + 'js/main-*.js',
      distPath + 'js/main-*.js.map'
    ], configs.del);
  });

  /**
   * 删除 fonts、icons、imgs
   */
  gulp.task('_del-assets', () => {
    return del([
      distPath + 'fonts/**/*',
      distPath + 'icons/**/*',
      distPath + 'imgs/**/*'
    ], configs.del);
  });

  /**
   * 复制字体
   */
  gulp.task('_copy-fonts', () => {
    return gulp.src('vendor/mdui/fonts/**/*')
      .pipe(gulp.dest(distPath + 'fonts/'));
  });

  /**
   * 复制图标
   */
  gulp.task('_copy-icons', () => {
    return gulp.src('vendor/mdui/icons/**/*')
      .pipe(gulp.dest(distPath + 'icons/'));
  });

  /**
   * 复制图片
   */
  gulp.task('_copy-imgs', () => {
    return gulp.src('imgs/**/*')
      .pipe(gulp.dest(distPath + 'imgs/'));
  });

  /**
   * 构建 vendor.js 文件
   */
  gulp.task('_build-vendor-js', () => {
    return gulp.src('./js/vendor.js')
      .pipe(jsImport(configs.jsImport))
      .pipe(rev())
      .pipe(gulp.dest(distPath + 'js/'))
      .pipe(rev.manifest(configs.revManifest))
      .pipe(gulp.dest('./'));
  });

  /**
   * 构建 main.js 文件
   */
  gulp.task('_build-main-js', () => {
    return gulp.src('./js/main.js')
      .pipe(sourcemaps.init())
      .pipe(jsImport(configs.jsImport))
      .pipe(rollup({
        plugins: [
          babel({
            exclude: 'node_modules/**'
          })
        ]
      }, {
        format: 'cjs'
      }))
      .pipe(uglify())
      .pipe(rev())
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest(distPath + 'js/'))
      .pipe(rev.manifest(configs.revManifest))
      .pipe(gulp.dest('./'))
  });

  /**
   * 构建 vendor.css 文件
   */
  gulp.task('_build-vendor-css', () => {
    return gulp.src('./css/vendor.less')
      .pipe(less())
      .pipe(cleanCSS(configs.cleanCSS))
      .pipe(rev())
      .pipe(gulp.dest(distPath + 'css/'))
      .pipe(rev.manifest(configs.revManifest))
      .pipe(gulp.dest('./'));
  });

  /**
   * 构建 main.css 文件
   */
  gulp.task('_build-main-css', () => {
    return gulp.src('./css/main.less')
      .pipe(sourcemaps.init())
      .pipe(less())
      .pipe(autoprefixer(configs.autoprefixer))
      .pipe(csscomb())
      .pipe(cleanCSS(configs.cleanCSS))
      .pipe(rev())
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest(distPath + 'css/'))
      .pipe(rev.manifest(configs.revManifest))
      .pipe(gulp.dest('./'))
  });

  /**
   * 构建 vender.js 和 vendor.css 文件
   */
  gulp.task('build-vendor', (cb) => {
    runSequence(
      [
        '_del-vendor-css',
        '_del-vendor-js'
      ],
      '_build-vendor-css',
      '_build-vendor-js',
      '_rev',
      cb
    );
  });

  /**
   * 构建 CSS 文件
   */
  gulp.task('build-css', (cb) => {
    runSequence(
      '_del-main-css',
      '_build-main-css',
      '_rev',
      cb
    );
  });

  /**
   * 构建 JS 文件
   */
  gulp.task('build-js', (cb) => {
    runSequence(
      '_del-main-js',
      '_build-main-js',
      '_rev',
      cb
    );
  });

  /**
   * 复制资源
   */
  gulp.task('build-assets', (cb) => {
    runSequence(
      '_del-assets',
      [
        '_copy-fonts',
        '_copy-icons',
        '_copy-imgs'
      ],
      cb
    );
  });

  /**
   * 构建
   */
  gulp.task('build', (cb) => {
    runSequence(
      [
        '_del-vendor-css',
        '_del-vendor-js',
        '_del-main-css',
        '_del-main-js',
        '_del-assets'
      ],
      [
        '_copy-fonts',
        '_copy-icons',
        '_copy-imgs'
      ],
      '_build-vendor-js',
      '_build-main-js',
      '_build-vendor-css',
      '_build-main-css',
      '_rev',
      cb
    );
  });

})();
