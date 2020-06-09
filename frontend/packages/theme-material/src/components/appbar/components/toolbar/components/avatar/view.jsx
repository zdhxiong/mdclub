import { h } from 'hyperapp';
import { location } from 'hyperapp-router';
import mdui from 'mdui';
import { fullPath } from '~/utils/path';
import { removeCookie } from '~/utils/cookie';
import './index.less';

import AvatarUpload from '~/components/avatar-upload/view.jsx';

let menu;

const Button = ({ user }) => (
  <div
    class="user mdui-btn mdui-btn-icon mdui-btn-dense"
    title={`帐号：${user.username}\n(${user.email})`}
    oncreate={(element) => {
      menu = new mdui.Menu(element, '#appbar-avatar-popover', {
        covered: false,
      });
    }}
  >
    <img src={user.avatar.middle} width="32" height="32" />
  </div>
);

const Popover = ({ user }) => (
  <div class="popover mdui-menu" id="appbar-avatar-popover">
    <div class="info">
      <div class="avatar-box">
        <AvatarUpload user={user} />
        <img src={user.avatar.large} width="96" height="96" />
      </div>
      <div class="username">{user.username}</div>
      <div class="email mdui-text-color-theme-secondary">{user.email}</div>
      <button
        class="personal mdui-btn mdui-btn-dense mdui-btn-outlined mdui-color-theme mdui-ripple"
        onclick={() => {
          const url = fullPath(`/users/${user.user_id}`);

          location.actions.go(url);
          menu.close();
        }}
      >
        个人资料
      </button>
    </div>
    <div class="bottom">
      <button
        onclick={() => {
          removeCookie('token');
          window.localStorage.removeItem('token');
          window.location.reload();
        }}
        class="mdui-btn mdui-btn-dense mdui-btn-outlined mdui-color-theme mdui-ripple"
      >
        退出
      </button>
    </div>
  </div>
);

export default ({ user }) => (
  <div class="avatar">
    <Button user={user} />
    <Popover user={user} />
  </div>
);
