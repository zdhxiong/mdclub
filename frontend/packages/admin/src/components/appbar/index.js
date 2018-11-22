import { h } from 'hyperapp';
import cc from 'classcat';
import mdui, { JQ as $ } from 'mdui';
import { Link } from '@hyperapp/router';
import jump from 'jump.js';
import './index.less';

/**
 * 初始化
 */
const init = (element) => {
  const $appbar = $(element);
  const $window = $(window);

  $appbar.mutation();

  // 移动端自动隐藏工具栏
  {
    const headroom = new mdui.Headroom($appbar, {
      pinnedClass: 'mdui-headroom-pinned-toolbar',
      unpinnedClass: 'mdui-headroom-unpinned-toolbar',
    });

    const handle = () => {
      if ($window.width() < 600) {
        headroom.enable();
      } else {
        headroom.disable();
      }
    };

    $window.on('resize', handle);
    handle();
  }

  // 点击应用栏回到页面顶部
  $appbar.on('click', (e) => {
    if (!window.pageYOffset) {
      return;
    }

    const $target = $(e.target);

    if ($target.is('.mdui-toolbar') || $target.is('.mdui-tab')) {
      jump(document.body, {
        duration: 300,
      });
    }
  });
};

export default () => (global_state, global_actions) => (
  <div
    class="mc-appbar mdui-appbar mdui-appbar-fixed mdui-shadow-0"
    oncreate={element => init(element)}
  >
    <div class="mdui-toolbar">

      <button
        mdui-drawer="{target: '.mc-drawer', swipe: true}"
        class="mdui-btn mdui-btn-icon mdui-ripple"
      >
        <i class="mdui-icon material-icons">menu</i>
      </button>

      <Link to={$.path('')} class="dashboard-link mdui-typo-title-opacity">
        <i class="dashboard-logo mdui-icon material-icons">dashboard</i>控制台
      </Link>

      <div class="mdui-toolbar-spacer"></div>

      <div class="user-menu">
        <div
          class="mdui-btn mdui-btn-icon mdui-btn-dense"
          title={`账号：${global_state.user.username}\n(${global_state.user.email})`}
          mdui-menu="{target: '#appbar-user-popover', covered: false}"
        >
          <img src={global_state.user.avatar.m} width="32" height="32"/>
        </div>
        <ul class="mdui-menu" id="appbar-user-popover">
          <li class="mdui-menu-item">
            <a href={`${window.G_ROOT}/`} class="mdui-ripple" target="_blank">
              <i class="mdui-menu-item-icon mdui-icon material-icons">home</i>查看站点
            </a>
          </li>
          <li class="mdui-menu-item">
            <a href={`${window.G_ROOT}/users/${global_state.user.user_id}`} class="mdui-ripple" target="_blank">
              <i class="mdui-menu-item-icon mdui-icon material-icons">person</i>我的主页
            </a>
          </li>
          <li class="mdui-menu-item">
            <a href="javascript:;" class="mdui-ripple">
              <i class="mdui-menu-item-icon mdui-icon material-icons">exit_to_app</i>退出登录
            </a>
          </li>
        </ul>
      </div>

    </div>
  </div>
);
