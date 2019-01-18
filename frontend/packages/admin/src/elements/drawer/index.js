import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
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
    to={$.path(url)}
    class={cc([
      'mdui-list-item',
      'mdui-ripple',
      {
        'mdui-list-item-active': $.isPathMatched(url, !url),
      },
    ])}
  >
    <i class="mdui-list-item-icon mdui-icon material-icons">{icon}</i>
    <div class="mdui-list-item-content">{title}</div>
  </Link>
);

const ChildHeader = () => (
  <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
    <i class="mdui-list-item-icon mdui-icon material-icons">delete</i>
    <div class="mdui-list-item-content">回收站</div>
    <i class="mdui-collapse-item-arrow mdui-icon material-icons">arrow_drop_down</i>
  </div>
);

const ChildItem = ({ url, title }) => (
  <Link
    to={$.path(url)}
    class={cc([
      'mdui-list-item',
      'mdui-ripple',
      {
        'mdui-list-item-active': $.isPathMatched(url, false),
      },
    ])}
  >{title}</Link>
);

export default () => (
  <div class="me-drawer mdui-drawer" key="me-drawer">
    <div
      class="mdui-list"
      mdui-collapse
      oncreate={element => $(element).mutation()}
    >
      <Item title="仪表盘" url="" icon="dashboard"/>
      <Divider/>
      <Item title="话题" url="/topics" icon="class"/>
      <Item title="提问" url="/questions" icon="question_answer"/>
      <Item title="回答" url="/answers" icon="reply_all"/>
      <Item title="文章" url="/articles" icon="description"/>
      <Item title="评论" url="/comments" icon="comment"/>
      <Item title="图片" url="/images" icon="image"/>
      <Divider/>
      <Item title="用户" url="/users" icon="people"/>
      <Item title="举报" url="/reports" icon="report"/>
      <Item url="/options" icon="settings" title="设置"/>
      <div class="mdui-collapse-item">
        <ChildHeader/>
        <div class="mdui-collapse-item-body mdui-list mdui-list-dense">
          <ChildItem url="/trash/users" title="用户"/>
          <ChildItem url="/trash/topics" title="话题"/>
          <ChildItem url="/trash/questions" title="提问"/>
          <ChildItem url="/trash/answers" title="回答"/>
          <ChildItem url="/trash/articles" title="文章"/>
          <ChildItem url="/trash/comments" title="评论"/>
        </div>
      </div>
    </div>
    <div class="toggle">
      <button onclick={toggleMini} class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon">
        <i class="mdui-icon material-icons">keyboard_capslock</i>
      </button>
    </div>
  </div>
);
