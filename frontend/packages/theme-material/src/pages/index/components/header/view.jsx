import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

export default ({ title, url }) => (
  <Link class="header mdui-ripple" to={fullPath(url)}>
    <div class="mdui-text-color-theme-secondary">{title}</div>
    <i class="mdui-icon material-icons mdui-text-color-theme">arrow_forward</i>
  </Link>
);
