import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import { Link } from '@hyperapp/router';
import './index.less';

const DrawerBtn = () => (
  <button
    class="mdui-btn mdui-btn-icon mdui-ripple"
    mdui-drawer="{target: '.mc-drawer', swipe: true}"
  >
    <i class="mdui-icon material-icons">menu</i>
  </button>
);

const HomeLink = () => (
  <Link to={$.path('')} class="link mdui-typo-title-opacity">
    <i class="logo mdui-icon material-icons">dashboard</i>控制台
  </Link>
);

const Spacer = () => (
  <div class="mdui-toolbar-spacer"></div>
);

const ThemeBtn = ({ onClick, theme }) => (
  <div class="theme">
    <button
      class="mdui-btn mdui-btn-icon"
      mdui-tooltip={theme === 'light' ? "{content: '夜间模式'}" : "{content: '日间模式'}"}
      onclick={onClick}
    >
      <i class="mdui-icon material-icons">{theme === 'light' ? 'brightness_2' : 'brightness_5'}</i>
    </button>
  </div>
);

const UserMenu = ({ user }) => (
  <div class="user">
    <div
      class="mdui-btn mdui-btn-icon mdui-btn-dense"
      title={`账号：${user.username}\n(${user.email})`}
      mdui-menu="{target: '#appbar-user-popover', covered: false}"
    >
      <img src={user.avatar.s} width="32" height="32"/>
    </div>
    <ul class="mdui-menu" id="appbar-user-popover">
      <li class="mdui-menu-item">
        <a href={`${window.G_ROOT}/`} class="mdui-ripple" target="_blank">
          <i class="mdui-menu-item-icon mdui-icon material-icons">home</i>查看站点
        </a>
      </li>
      <li class="mdui-menu-item">
        <a href={`${window.G_ROOT}/users/${user.user_id}`} class="mdui-ripple" target="_blank">
          <i class="mdui-menu-item-icon mdui-icon material-icons">person</i>我的主页
        </a>
      </li>
      <li class="mdui-menu-item">
        <a href="" class="mdui-ripple">
          <i class="mdui-menu-item-icon mdui-icon material-icons">exit_to_app</i>退出登录
        </a>
      </li>
    </ul>
  </div>
);

export default () => (global_state, global_actions) => {
  const state = global_state.lazyComponents.appbar;
  const actions = global_actions.lazyComponents.appbar;

  return () => (
    <div
      class="mc-appbar mdui-appbar mdui-appbar-fixed mdui-shadow-0"
      oncreate={element => actions.init({ element })}
    >
      <div class="mdui-toolbar">
        <DrawerBtn/>
        <HomeLink/>
        <Spacer/>
        <ThemeBtn
          onClick={actions.toggleTheme}
          theme={state.theme}
        />
        {state.user.username ? <UserMenu user={state.user}/> : ''}
      </div>
    </div>
  );
};
