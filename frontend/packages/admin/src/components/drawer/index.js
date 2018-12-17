import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
import './index.less';

const $body = $('body');

// 切换抽屉栏缩小状态
const toggle = () => {
  if ($body.hasClass('mdui-drawer-body-left-mini')) {
    $body.removeClass('mdui-drawer-body-left-mini');
  } else {
    $body.addClass('mdui-drawer-body-left-mini');
  }
};

const RootItem = ({ url, icon, title }) => (
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
  <div class="mc-drawer mdui-drawer" key="mc-drawer">
    <div
      class="mdui-list"
      mdui-collapse
      oncreate={element => $(element).mutation()}
    >
      <RootItem url="" icon="home" title="首页"/>
      <div class="mdui-divider"></div>
      <RootItem url="/topics" icon="class" title="话题"/>
      <RootItem url="/users" icon="people" title="用户"/>
      <RootItem url="/questions" icon="question_answer" title="提问"/>
      <RootItem url="/answers" icon="reply_all" title="回答"/>
      <RootItem url="/articles" icon="description" title="文章"/>
      <RootItem url="/comments" icon="comment" title="评论"/>
      <RootItem url="/images" icon="image" title="图片"/>
      <div class="mdui-divider"></div>
      <RootItem url="/reports" icon="report" title="举报"/>
      <div class="mdui-collapse-item">
        <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
          <i class="mdui-list-item-icon mdui-icon material-icons">delete</i>
          <div class="mdui-list-item-content">回收站</div>
          <i class="mdui-collapse-item-arrow mdui-icon material-icons">arrow_drop_down</i>
        </div>
        <div class="mdui-collapse-item-body mdui-list mdui-list-dense">
          <ChildItem url="/trash/users" title="用户"/>
          <ChildItem url="/trash/topics" title="话题"/>
          <ChildItem url="/trash/questions" title="提问"/>
          <ChildItem url="/trash/answers" title="回答"/>
          <ChildItem url="/trash/articles" title="文章"/>
          <ChildItem url="/trash/comments" title="评论"/>
        </div>
      </div>
      <div class="mdui-divider"></div>
      <RootItem url="/options" icon="settings" title="设置"/>
    </div>
    <div class="toggle" onclick={toggle}>
      <button class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon">
        <i class="mdui-icon material-icons">keyboard_capslock</i>
      </button>
    </div>
  </div>
);
