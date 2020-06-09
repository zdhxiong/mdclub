import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

import Follow from '~/components/follow/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';

export default ({ user, actions }) => (
  <div class="item-inner" key={user.user_id}>
    <div class="item mdui-card">
      <Link
        to={fullPath(`/users/${user.user_id}`)}
        class="mdui-ripple info"
        onclick={() => actions.afterItemClick(user)}
      >
        <div
          class="avatar"
          style={{
            backgroundImage: `url("${user.avatar.large}")`,
          }}
        />
        <div class="username mdui-text-color-theme-text">{user.username}</div>
        <div class="headline mdui-text-color-theme-secondary">
          {user.headline}
        </div>
      </Link>
      <div class="actions">
        <If condition={!user.relationships.is_me}>
          <Follow
            item={user}
            type="users"
            id={user.user_id}
            actions={actions}
          />
        </If>
        <div class="flex-grow" />
        <OptionsButton type="user" item={user} />
      </div>
    </div>
  </div>
);
