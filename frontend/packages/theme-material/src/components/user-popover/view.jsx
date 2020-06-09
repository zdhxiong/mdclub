import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import $ from 'mdui.jq';
import { unescape } from 'html-escaper';
import { fullPath } from '~/utils/path';
import { emit } from '~/utils/pubsub';
import currentUser from '~/utils/currentUser';
import './index.less';

import Follow from '~/components/follow/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';

const StatItem = ({ url, label, count }) => (
  <Link to={url} class="mdui-btn mdui-ripple">
    <label class="mdui-text-color-theme-secondary">{label}</label>
    <span class="mdui-text-color-theme-text">{count}</span>
  </Link>
);

export default (
  { actions, user, dataName, primaryKey = null, primaryValue = null },
  children,
) => (
  <div
    class="mc-user-popover"
    key="mc-user-popover"
    onclick={(event) => event.preventDefault()}
    oncreate={(element) => {
      actions.onUserPopoverCreate({
        element,
        dataName,
        primaryKey,
        primaryValue,
      });
    }}
  >
    {children}
    <div class="popover mdui-menu" key="popover">
      <If condition={user.cover}>
        <div class="mc-user-line">
          <Link
            key="avatar"
            class="avatar user-popover-trigger"
            to={fullPath(`/users/${user.user_id}`)}
            style={{
              backgroundImage: `url("${user.avatar.middle}")`,
            }}
          />
          <Link
            key="username"
            class="username user-popover-trigger mdui-text-color-theme-text"
            to={fullPath(`/users/${user.user_id}`)}
          >
            {user.username}
          </Link>
          <div key="headline" class="headline mdui-text-color-theme-secondary">
            {unescape(user.headline)}
          </div>
        </div>
        <div class="stats">
          <StatItem
            url={fullPath(`/users/${user.user_id}#answers`)}
            label="回答"
            count={user.answer_count}
          />
          <StatItem
            url={fullPath(`/users/${user.user_id}#articles`)}
            label="文章"
            count={user.article_count}
          />
          <button
            class="mdui-btn mdui-ripple"
            onclick={() => {
              emit('users_dialog_open', {
                type: 'followers',
                id: user.user_id,
              });
            }}
          >
            <label class="mdui-text-color-theme-secondary">关注者</label>
            <span class="mdui-text-color-theme-text">
              {user.follower_count}
            </span>
          </button>
        </div>
        <div class="bottom">
          <If
            condition={!currentUser() || currentUser().user_id !== user.user_id}
          >
            <Follow
              item={user}
              type="relationships-user"
              dataName={dataName}
              primaryKey={primaryKey}
              id={primaryValue}
              actions={actions}
            />
          </If>
          <div class="flex-grow" />
          <OptionsButton type="user" item={user} />
        </div>
      </If>
      <If condition={!user.cover}>
        <div
          key="mc-user-popover-spinner"
          class="mdui-spinner"
          oncreate={(element) => $(element).mutation()}
        />
      </If>
    </div>
  </div>
);
