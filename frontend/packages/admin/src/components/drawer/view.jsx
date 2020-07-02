import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from 'hyperapp-router';
import { $body } from 'mdui/es/utils/dom';
import './index.less';
import {
  fullPath,
  isPathAnswers,
  isPathArticles,
  isPathComments,
  isPathIndex,
  isPathOption,
  isPathOptions,
  isPathQuestions,
  isPathReports,
  isPathTopics,
  isPathUsers,
} from '~/utils/path';

const Divider = () => <div class="mdui-divider" />;

const Item = ({ title, url, icon, active }) => (
  <Link
    to={fullPath(url)}
    class={cc([
      'mdui-list-item',
      'mdui-ripple',
      { 'mdui-list-item-active': active },
    ])}
  >
    <i class="mdui-list-item-icon mdui-icon material-icons">{icon}</i>
    <div class="mdui-list-item-content">{title}</div>
  </Link>
);

export default () => (
  <div class="mc-drawer mdui-drawer" key="mc-drawer">
    <div class="mdui-list">
      <Item title="仪表盘" url="" icon="dashboard" active={isPathIndex()} />
      <Divider />
      <Item title="话题" url="/topics" icon="class" active={isPathTopics()} />
      <Item
        title="提问"
        url="/questions"
        icon="question_answer"
        active={isPathQuestions()}
      />
      <Item
        title="回答"
        url="/answers"
        icon="reply_all"
        active={isPathAnswers()}
      />
      <Item
        title="文章"
        url="/articles"
        icon="description"
        active={isPathArticles()}
      />
      <Item
        title="评论"
        url="/comments"
        icon="comment"
        active={isPathComments()}
      />
      <Divider />
      <Item title="用户" url="/users" icon="people" active={isPathUsers()} />
      <Item
        title="举报"
        url="/reports"
        icon="report"
        active={isPathReports()}
      />
      <Item
        title="设置"
        url="/options"
        icon="settings"
        active={isPathOptions() || isPathOption()}
      />
    </div>
    <div class="toggle">
      <button
        onclick={() => $body.toggleClass('mdui-drawer-body-left-mini')}
        class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon"
      >
        <i class="mdui-icon material-icons">keyboard_capslock</i>
      </button>
    </div>
  </div>
);
