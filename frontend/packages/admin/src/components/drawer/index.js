import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
import './index.less';

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

export default () => (
  <div class="mc-drawer mdui-drawer">
    <div
      class="mdui-list"
      mdui-collapse
      oncreate={element => $(element).mutation()}
    >
      <Item url="" icon="home" title="首页"/>
      <div class="mdui-divider"></div>
      <Item url="/topics" icon="class" title="话题"/>
      <Item url="/users" icon="people" title="用户"/>
      <Item url="/questions" icon="question_answer" title="提问"/>
      <Item url="/answers" icon="reply_all" title="回答"/>
      <Item url="/articles" icon="description" title="文章"/>
      <Item url="/comments" icon="comment" title="评论"/>
      <Item url="/images" icon="image" title="图片"/>
      <div class="mdui-divider"></div>
      <Item url="/reports" icon="report" title="举报"/>
      <Item url="/options" icon="settings" title="设置"/>
    </div>
  </div>
);
