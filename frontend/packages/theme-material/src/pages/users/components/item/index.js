import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import cc from 'classcat';
import { JQ as $ } from 'mdui';
import './index.less';

export default ({ user }) => (global_state, global_actions) => (
  <div
    class="mdui-col"
    key={user.user_id}
    oncreate={element => $(element).mutation()}
  >
    <div class="mdui-card item">
      <div
        class="mdui-ripple info"
        onclick={() => {
          global_actions.users.saveScrollPosition();
          location.actions.go($.path(`/users/${user.user_id}`));
        }}
      >
        <div class="avatar" style={{
          backgroundImage: `url("${user.avatar.l}")`,
        }}></div>
        <div class="username">{user.username}</div>
        <div class="headline">{user.headline}</div>
      </div>
      <div class="actions">
        {!user.relationship.is_me ?
          <button
            class={cc([
              'mdui-btn',
              'mdui-text-color-blue',
              'mdui-btn-block',
              {
                following: user.relationship.is_following,
              },
            ])}
            onclick={() => { global_actions.users.toggleFollow(user.user_id); }}
          >{user.relationship.is_following ? '已关注' : '关注'}</button> : ''}
      </div>
    </div>
  </div>
);
