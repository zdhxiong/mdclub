import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
import path from '../../helper/path';
import './index.less';

const $body = $('body');

const toggleMini = () => {
  $body.toggleClass('mdui-drawer-body-left-mini');
};

const Divider = () => (
  <div class="mdui-divider"></div>
);

const Item = ({ url, icon, title }) => (
  <Link
    to={path.addPrefix(url)}
    class={cc([
      'mdui-list-item',
      'mdui-ripple',
      { 'mdui-list-item-active': path.isMatched(url, !url) },
    ])}
  >
    <i class="mdui-list-item-icon mdui-icon material-icons">{icon}</i>
    <div class="mdui-list-item-content">{title}</div>
  </Link>
);

export default () => (
  <div class="me-drawer mdui-drawer" key="me-drawer">
    <div class="mdui-list">
      <Item title="仪表盘" url="" icon="dashboard"/>
      <Divider/>
      <Item title="话题" url="/topics" icon="class"/>
      <Item title="提问" url="/questions" icon="question_answer"/>
      <Item title="回答" url="/answers" icon="reply_all"/>
      <Item title="文章" url="/articles" icon="description"/>
      <Item title="评论" url="/comments" icon="comment"/>
      <Divider/>
      <Item title="用户" url="/users" icon="people"/>
      <Item title="举报" url="/reports" icon="report"/>
      <Item title="设置" url="/options" icon="settings"/>
    </div>
    <div class="toggle">
      <button onclick={toggleMini} class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon">
        <i class="mdui-icon material-icons">keyboard_capslock</i>
      </button>
    </div>
  </div>
);
