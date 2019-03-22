import { JQ as $ } from 'mdui';

$.extend({
  /**
   * 开始全屏 loading 状态
   */
  loadStart: () => {
    if ($('.mc-loading-overlay').length) {
      return;
    }

    const $overlay = $('<div class="mc-loading-overlay"><div class="mdui-spinner mdui-spinner-colorful"></div></div>');

    $overlay
      .appendTo(document.body)
      .reflow()
      .addClass('mc-loading-overlay-show');

    setTimeout(() => $overlay.mutation());

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
  path: url => window.G_ADMIN_ROOT + url,

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
});

// 控制台安全提示
if (window.top === window && window.console && !('ActiveXObject' in window)) {
  setTimeout(() => {
    window.console.log('%c%s', 'color: red; background: yellow; font-size: 24px;', '警告！');
    window.console.log('%c%s', 'color: black; font-size: 18px;', `使用此控制台可能会让攻击者利用 Self-XSS（自跨站脚本）攻击来冒充你，并窃取你的信息。
请勿输入或粘贴你不明白的代码。`);
  }, 0);
}
