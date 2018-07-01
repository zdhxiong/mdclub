import { h } from 'hyperapp';
import { Link } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
import './index.less';

import timeHelper from '../../helper/time';

export default ({ user, time }) => (
  <div class="mc-user-line">
    <Link
      class="avatar"
      to={$.path(`/users/${user.user_id}`)}
      style={{
        backgroundImage: `url("${user.avatar.m}")`,
      }}
    ></Link>
    <div class="info">
      <div class="username">
        <Link to={$.path(`/users/${user.user_id}`)}>{user.username}</Link>
      </div>
      <div class="headline">{user.headline}</div>
    </div>
    <div class="more">
      <span class="time" title={timeHelper.format(time)}>{timeHelper.friendly(time)}</span>
    </div>
  </div>
);
