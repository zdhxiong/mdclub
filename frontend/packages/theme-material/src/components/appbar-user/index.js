import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import mdui from 'mdui';
import $ from 'mdui.JQ';
import Cookies from 'js-cookie';
import './index.less';
import AvatarUpload from '../../components/avatar-upload';

export default () => global_state => (
  <div class="mc-appbar-user">
    <div
      class="mdui-btn mdui-btn-icon mdui-btn-dense user"
      title={`帐号：${global_state.user.user.username}\n(${global_state.user.user.email})`}
      mdui-menu="{target: '#appbar-user-popover', covered: false}"
    >
      <img src={global_state.user.user.avatar.m} width="32" height="32"/>
    </div>
    <div class="mdui-menu popover" id="appbar-user-popover">
      <div class="info">
        <div class="avatar">
          <AvatarUpload/>
          <img src={global_state.user.user.avatar.l} width="96" height="96"/>
        </div>
        <div class="username">{global_state.user.user.username}</div>
        <div class="email">{global_state.user.user.email}</div>
        <button
          onclick={() => {
            location.actions.go($.path(`/users/${global_state.user.user.user_id}`));
            (new mdui.Menu('.mc-appbar-user .user', '#appbar-user-popover')).close();
          }}
          class="mdui-btn mdui-btn-dense mdui-btn-raised mdui-ripple mdui-color-blue mdui-text-color-white personal"
        >个人资料</button>
      </div>
      <div class="bottom">
        <button
          onclick={() => {
            Cookies.remove('token');
            window.location.reload();
          }}
          class="mdui-btn mdui-btn-dense mdui-btn-raised mdui-ripple mdui-ripple-black mdui-color-white mdui-float-right"
        >退出</button>
      </div>
    </div>
  </div>
);
