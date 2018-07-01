import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from '@hyperapp/router';
import $ from 'mdui.JQ';
import './index.less';

const Item = ({ url, icon, title }) => (
  <Link
    to={$.path(url)}
    class={cc([
      'mdui-list-item',
      'mdui-ripple',
      {
        'mdui-text-color-theme mdui-list-item-active': $.isPathMatched(url),
      },
    ])}
  >
    <i class={cc([
      'mdui-list-item-icon',
      'mdui-icon',
      'material-icons',
    ])}>{icon}</i>
    <div class="mdui-list-item-content">{title}</div>
  </Link>
);

export default () => global_state => (
  <div class="mc-drawer mdui-drawer">
    <div class="mdui-list">
      <Item url="/" icon="home" title="首页"/>
      <Item url="/questions" icon="forum" title="问答"/>
      {/*<Item url="/articles" icon="description" title="文章"/>*/}
      {/*<Item url="/topics" icon="class" title="话题"/>*/}
      {global_state.user.user.user_id ? <div class="mdui-divider"></div> : ''}
      {global_state.user.user.user_id ? <Item url={`/users/${global_state.user.user.user_id}`} icon="account_circle" title="个人资料"/> : ''}
      {global_state.user.user.user_id ? <Item url="/users" icon="people" title="人脉"/> : ''}
      {/*global_state.user.user.user_id ? <Item url="/notifications" icon="notifications" title="通知"/> : ''*/}
      {/*global_state.user.user.user_id ? <Item url="/inbox" icon="mail" title="私信"/> : ''*/}
    </div>
    <div class="copyright">
    </div>
  </div>
);
