import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import cc from 'classcat';
import { Link } from '@hyperapp/router';
import './index.less';

import SearchBar from '../../components/searchbar/view';

const DrawerBtn = () => (
  <button
    class="drawer mdui-btn mdui-btn-icon mdui-ripple"
    mdui-drawer="{target: '.me-drawer', swipe: true}"
  >
    <i class="mdui-icon material-icons">menu</i>
  </button>
);

const HomeLink = () => (
  <Link to={$.path('')} class="link mdui-typo-title-opacity">
    <i class="logo mdui-icon material-icons">dashboard</i>控制台
  </Link>
);

const ThemeBtn = ({ onClick, theme }) => (
  <div class="theme mdui-text-color-theme-icon">
    <button
      class="mdui-btn mdui-btn-icon"
      mdui-tooltip={theme === 'light' ? "{content: '夜间模式'}" : "{content: '日间模式'}"}
      onclick={onClick}
    >
      <i class="mdui-icon material-icons">{theme === 'light' ? 'brightness_2' : 'brightness_5'}</i>
    </button>
  </div>
);

const UserMenuItem = ({ icon, name, href, onClick }) => (
  <li class="mdui-menu-item">
    <a
      class="mdui-ripple"
      href={href}
      target={href ? '_blank' : false}
      onclick={onClick}
    >
      <i class="mdui-menu-item-icon mdui-icon material-icons mdui-text-color-theme-icon">{icon}</i>{name}
    </a>
  </li>
);

const UserMenu = ({ user, logout }) => (
  <div class="user">
    <div
      class="mdui-btn mdui-btn-icon mdui-btn-dense"
      title={`账号：${user.username}\n(${user.email})`}
      mdui-menu="{target: '#appbar-user-popover', covered: false}"
    >
      <img src={user.avatar.s} width="32" height="32"/>
    </div>
    <ul class="mdui-menu" id="appbar-user-popover">
      <UserMenuItem icon="home" name="查看站点" href={`${window.G_ROOT}/`}/>
      <UserMenuItem icon="person" name="我的主页" href={`${window.G_ROOT}/users/${user.user_id}`}/>
      <UserMenuItem icon="exit_to_app" name="退出登录" onClick={logout}/>
    </ul>
  </div>
);

export default () => (global_state, global_actions) => {
  const state = global_state.components.appbar;
  const actions = global_actions.components.appbar;

  return () => (
    <div
      class={cc([
        'mc-appbar mdui-appbar mdui-appbar-fixed',
        {
          shadow: state.shadow,
        },
      ])}
      oncreate={element => actions.init({ element })}
    >
      <div class="mdui-toolbar">
        <DrawerBtn/>
        <HomeLink/>
        <SearchBar/>
        <ThemeBtn onClick={actions.toggleTheme} theme={state.theme}/>
        <If condition={state.user.username}>
          <UserMenu user={state.user} logout={actions.logout}/>
        </If>
      </div>
    </div>
  );
};
