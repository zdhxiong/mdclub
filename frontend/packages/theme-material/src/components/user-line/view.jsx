import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { unescape } from 'html-escaper';
import { timeFormat, timeFriendly } from '~/utils/time';
import { fullPath } from '~/utils/path';
import './index.less';

import UserPopover from '~/components/user-popover/view.jsx';

/**
 * @param actions
 * @param user 用户信息
 * @param time 时间戳
 * @param dataName state 中的数据字段名
 * @param primaryKey
 * @param primaryValue
 */
export default ({
  actions,
  user,
  time,
  dataName = null,
  primaryKey = null,
  primaryValue = null,
}) => (
  <div class="mc-user-line">
    <UserPopover
      actions={actions}
      user={user}
      dataName={dataName}
      primaryKey={primaryKey}
      primaryValue={primaryValue}
    >
      <Link
        class="avatar user-popover-trigger"
        to={fullPath(`/users/${user.user_id}`)}
        style={{
          backgroundImage: `url("${user.avatar.middle}")`,
        }}
      />
      <Link
        class="username user-popover-trigger mdui-text-color-theme-text"
        to={fullPath(`/users/${user.user_id}`)}
      >
        {user.username}
      </Link>
      <div class="headline mdui-text-color-theme-secondary">
        {unescape(user.headline)}
      </div>
      <If condition={time}>
        <div class="more">
          <span
            class="time mdui-text-color-theme-secondary"
            title={timeFormat(time)}
          >
            {timeFriendly(time)}
          </span>
        </div>
      </If>
    </UserPopover>
  </div>
);
