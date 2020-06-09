import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

export default ({ count }) => (
  <Link
    to={fullPath('/notifications')}
    class="notification mdui-btn mdui-btn-icon"
    title={`${count ? `${count} 条` : '没有'}未读通知`}
  >
    <If condition={count}>
      <span class="count" />
    </If>
    <i class="mdui-icon material-icons mdui-text-color-theme-icon">
      notifications
    </i>
  </Link>
);
