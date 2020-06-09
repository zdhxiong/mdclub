import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import cc from 'classcat';
import $ from 'mdui.jq';
import { timeFormat, timeFriendly } from '~/utils/time';
import { fullPath } from '~/utils/path';
import { summaryText } from '~/utils/html';
import './index.less';

import UserPopover from '~/components/user-popover/view.jsx';

const Wrapper = ({ item, id, last_visit_id, path, actions }, children) => (
  <Link
    to={fullPath(path)}
    class={cc([
      'mc-list-item',
      {
        'last-visit': last_visit_id === id,
      },
    ])}
    key={id}
    oncreate={(element) => $(element).mutation()}
    onclick={() => {
      actions.afterItemClick(item);
    }}
  >
    {children}
  </Link>
);

const Avatar = ({ actions, user, tabName, primaryKey, id }) => (
  <UserPopover
    actions={actions}
    user={user}
    dataName={`${tabName}_data`}
    primaryKey={primaryKey}
    primaryValue={id}
  >
    <div
      class="avatar user-popover-trigger"
      style={{
        backgroundImage: `url("${user.avatar.middle}")`,
      }}
    />
  </UserPopover>
);

const Title = ({ content }) => (
  <div
    class="title mdui-text-color-theme-text"
    oncreate={summaryText(content)}
    onupdate={summaryText(content)}
  />
);

const Content = ({ snippet, updateTime, updateTimeDesc, replys }) => (
  <div class="content mdui-text-color-theme-secondary">
    <div
      class="snippet"
      oncreate={summaryText(snippet, 100)}
      onupdate={summaryText(snippet, 100)}
    />
    <div class="meta">
      <div class="update_time" title={timeFormat(updateTime)}>
        {`${updateTimeDesc} ${timeFriendly(updateTime)}`}
      </div>
      <div class="replys">{replys}</div>
    </div>
  </div>
);

/**
 * 用于提问列表
 */
export const QuestionItem = ({ question, last_visit_id, tabName, actions }) => (
  <Wrapper
    item={question}
    id={question.question_id}
    last_visit_id={last_visit_id}
    path={`/questions/${question.question_id}`}
    actions={actions}
  >
    <Avatar
      actions={actions}
      user={question.relationships.user}
      tabName={tabName}
      primaryKey="question_id"
      id={question.question_id}
    />
    <Title content={question.title} />
    <Content
      snippet={question.content_rendered}
      updateTime={question.answer_time || question.create_time}
      updateTimeDesc={question.answer_time ? '回复于' : '发布于'}
      replys={`${question.answer_count} 个回答`}
    />
  </Wrapper>
);

/**
 * 用于文章列表
 */
export const ArticleItem = ({ article, last_visit_id, tabName, actions }) => (
  <Wrapper
    item={article}
    id={article.article_id}
    last_visit_id={last_visit_id}
    path={`/articles/${article.article_id}`}
    actions={actions}
  >
    <Avatar
      actions={actions}
      user={article.relationships.user}
      tabName={tabName}
      primaryKey="article_id"
      id={article.article_id}
    />
    <Title content={article.title} />
    <Content
      snippet={article.content_rendered}
      updateTime={article.create_time}
      updateTimeDesc="发布于"
      replys={`${article.comment_count} 条评论`}
    />
  </Wrapper>
);

export const AnswerItem = ({ answer, last_visit_id, tabName, actions }) => (
  <Wrapper
    item={answer}
    id={answer.answer_id}
    last_visit_id={last_visit_id}
    path={`/questions/${answer.question_id}/answers/${answer.answer_id}`}
    actions={actions}
  >
    <Avatar
      actions={actions}
      user={answer.relationships.user}
      tabName={tabName}
      primaryKey="answer_id"
      id={answer.answer_id}
    />
    <Title content={answer.relationships.question.title} />
    <Content
      snippet={answer.content_rendered}
      updateTime={answer.create_time}
      updateTimeDesc="发布于"
      replys={`${answer.comment_count} 条评论`}
    />
  </Wrapper>
);
