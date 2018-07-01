import mdui from 'mdui';
import $ from 'mdui.JQ';
import Cookies from 'js-cookie';

// 判断是否支持 webp
const isSupportWebp = !![].map && document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0;

/**
 * 设置全局 ajax 参数
 */
$.ajaxSetup({
  contentType: 'application/json',
  dataType: 'json',
  headers: {
    token: Cookies.get('token'),
    // 若支持 webp，则在 Accept 中添加 image/webp ，以便服务端根据该参数返回对应图片
    Accept: `application/json, text/javascript${isSupportWebp ? ', image/webp' : ''}`,
  },
});

/**
 * ajax 全局事件
 */
$(document).ajaxError(() => {
  mdui.snackbar('网络连接失败');
});

$.extend({
  /**
   * 开始全屏 loading 状态
   */
  loadStart: () => {
    if ($('.mc-loading-overlay').length) {
      return;
    }

    $('<div class="mc-loading-overlay"><div class="mdui-spinner mdui-spinner-colorful"></div></div>')
      .appendTo(document.body)
      .reflow()
      .addClass('mc-loading-overlay-show');

    setTimeout(() => {
      mdui.mutation();
    }, 0);

    $.lockScreen();
  },

  /**
   * 结束全屏 loading 状态
   */
  loadEnd: () => {
    const $overlay = $('.mc-loading-overlay');

    if (!$overlay.length) {
      return;
    }

    $overlay
      .removeClass('mc-loading-overlay-show')
      .transitionEnd(() => {
        $overlay.remove();
        $.unlockScreen();
      });
  },

  /**
   * 为链接补充前缀
   * @param url
   * @returns {string}
   */
  path: url => window.G_ROOT + url,

  /**
   * 判断当前路由是否和指定路径匹配
   * @param url
   * @param absolute 是否绝对匹配，若为 false，则只匹配前缀
   * @return {boolean}
   */
  isPathMatched: (url, absolute = true) => {
    if (absolute) {
      return window.location.pathname === $.path(url);
    }

    return window.location.pathname.indexOf($.path(url)) === 0;
  },

  /**
   * 判断指定颜色是否是深色的
   * @param color
   * @returns {*}
   */
  isDark: (color) => {
    const colors = {
      red: true,
      pink: true,
      purple: true,
      'deep-purple': true,
      indigo: true,
      blue: false,
      'light-blue': false,
      cyan: false,
      teal: true,
      green: false,
      'light-green': false,
      lime: false,
      yellow: false,
      amber: false,
      orange: false,
      'deep-orange': false,
      brown: true,
      grey: false,
      'blue-grey': true,
    };

    if (typeof colors[color] === 'undefined') {
      return false;
    }

    return colors[color];
  },

  /**
   * 根据主色返回 ripple 类名
   * @param color
   * @returns {string}
   */
  rippleClass: (color) => {
    let cls = 'mdui-ripple';

    if ($.isDark(color)) {
      cls += ' mdui-ripple-white';
    }

    return cls;
  },
});

// 控制台安全提示
if (window.top === window && window.console && !('ActiveXObject' in window)) {
  setTimeout(() => {
    window.console.log('%c%s', 'color: red; background: yellow; font-size: 24px;', '警告！');
    window.console.log('%c%s', 'color: black; font-size: 18px;', `使用此控制台可能会让攻击者利用 Self-XSS（自跨站脚本）攻击来冒充你，并窃取你的信息。
请勿输入或粘贴你不明白的代码。`);
  }, 0);
}
