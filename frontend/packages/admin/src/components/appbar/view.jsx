import { h } from 'hyperapp';
import cc from 'classcat';
import mdui from 'mdui';
import $ from 'mdui.jq';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

const UserMenuItem = ({ icon, name, href, onClick }) => (
  <li class="mdui-menu-item">
    <a
      class="mdui-ripple"
      href={href}
      target={href ? '_blank' : false}
      onclick={onClick}
    >
      <i class="mdui-menu-item-icon mdui-icon material-icons mdui-text-color-theme-icon">
        {icon}
      </i>
      {name}
    </a>
  </li>
);

export default ({ state, actions }, searchBar) => {
  const { shadow, user } = state;

  return (
    <div
      class={cc(['mc-appbar', 'mdui-appbar', 'mdui-appbar-fixed', { shadow }])}
      oncreate={(element) => actions.onCreate({ element })}
      key="mc-appbar"
    >
      <div class="mdui-toolbar">
        <button
          class="drawer mdui-btn mdui-btn-icon mdui-ripple"
          oncreate={(element) => {
            const $drawer = $('.mc-drawer');
            const drawer = new mdui.Drawer($drawer, { swipe: true });

            $drawer.data('drawer-instance', drawer);
            $(element).on('click', () => drawer.toggle());
          }}
        >
          <i class="mdui-icon material-icons">menu</i>
        </button>

        <Link to={fullPath('')} class="logo">
          <i class="mdui-icon material-icons">dashboard</i>控制台
        </Link>

        {searchBar}

        <If condition={user}>
          <div class="user">
            <div
              class="mdui-btn mdui-btn-icon mdui-btn-dense"
              title={`账号：${user.username}\n(${user.email})`}
              mdui-menu="{target: '#appbar-user-popover', covered: false}"
            >
              <img src={user.avatar.small} width="32" height="32" />
            </div>
            <ul class="mdui-menu" id="appbar-user-popover">
              <UserMenuItem
                name="查看站点"
                icon="home"
                href={`${window.G_ROOT}/`}
              />
              <UserMenuItem
                name="我的主页"
                icon="person"
                href={`${window.G_ROOT}/users/${user.user_id}`}
              />
              <UserMenuItem
                name="退出登录"
                icon="exit_to_app"
                onClick={actions.logout}
              />
            </ul>
          </div>
        </If>
      </div>
    </div>
  );
};
