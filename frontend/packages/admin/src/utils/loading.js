import $ from 'mdui.jq';

/**
 * 开始全屏 loading 状态
 */
export function loadStart() {
  if ($('.mc-loading-overlay').length) {
    return;
  }

  $(
    '<div class="mc-loading-overlay"><div class="mdui-spinner mdui-spinner-colorful"></div></div>',
  )
    .appendTo(document.body)
    .reflow()
    .addClass('mc-loading-overlay-show');

  setTimeout(() => {
    $('.mc-loading-overlay').mutation();
  }, 0);

  $.lockScreen();
}

/**
 * 结束全屏 loading 状态
 */
export function loadEnd() {
  const $overlay = $('.mc-loading-overlay');

  if (!$overlay.length) {
    return;
  }

  $overlay.removeClass('mc-loading-overlay-show').transitionEnd(() => {
    $overlay.remove();
    $.unlockScreen();
  });
}
