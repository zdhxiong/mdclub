import { JQ as $ } from 'mdui';

/**
 * 开启全屏加载中状态
 */
const start = () => {
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
};

/**
 * 关闭全屏加载中状态
 */
const end = () => {
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
};

export default {
  start,
  end,
};
