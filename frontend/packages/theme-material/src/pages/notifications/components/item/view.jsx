import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';
import { plainText, richText, summaryText } from '~/utils/html';

import Loading from '~/components/loading/view.jsx';

export default ({ notification, actions }) => {
  // eslint-disable-next-line no-empty-pattern
  const Title = ({}, children) => (
    <div class="title mdui-text-color-theme-text">{children}</div>
  );

  const Content = ({ content }) => {
    if (!notification.is_show_detail) {
      return (
        <div
          class="content"
          key="summary"
          oncreate={summaryText(content, 46)}
          onupdate={summaryText(content, 46)}
        />
      );
    }

    if (notification.is_loaded_detail) {
      const contentFunc =
        [
          'comment_deleted',
          'question_commented',
          'article_commented',
          'answer_commented',
          'comment_replies',
        ].indexOf(notification.type) > -1
          ? plainText(notification.content_detail)
          : richText(notification.content_detail);

      return (
        <div
          class="content mdui-typo"
          key="richtext"
          oncreate={contentFunc}
          onupdate={contentFunc}
        />
      );
    }

    return (
      <div class="content" key="loading">
        <Loading show={true} />
      </div>
    );
  };

  const More = () => (
    <div
      class="mdui-btn mdui-btn-icon more"
      title={notification.is_show_detail ? '隐藏详细信息' : '显示详细信息'}
      onclick={() => actions.toggleDetail(notification)}
    >
      <i class="mdui-icon material-icons">
        {notification.is_show_detail ? 'arrow_drop_up' : 'arrow_drop_down'}
      </i>
    </div>
  );

  const Delete = () => (
    <div
      class="mdui-btn mdui-btn-icon delete"
      title="删除该通知"
      onclick={() => actions.deleteOne(notification)}
    >
      <i class="mdui-icon material-icons">close</i>
    </div>
  );

  // eslint-disable-next-line no-empty-pattern
  const Item = ({}, children) => (
    <div class="item">
      <Delete />
      {children}
      <More />
    </div>
  );

  // eslint-disable-next-line no-empty-pattern
  const ReplyWrapper = ({}, children) => (
    <Item>
      {children}
      <Content content={notification.relationships.reply.content_summary} />
    </Item>
  );

  // eslint-disable-next-line no-empty-pattern
  const CommentDeletedWrapper = ({}, children) => (
    <Item>
      {children}
      <Content content={notification.content_deleted.content} />
    </Item>
  );

  const UserLink = () => (
    <Link to={fullPath(`/users/${notification.sender_id}`)} class="user">
      {notification.relationships.sender.username}
    </Link>
  );

  const QuestionLink = () => (
    <Link
      to={fullPath(`/questions/${notification.question_id}`)}
      class="question"
    >
      {notification.relationships.question
        ? notification.relationships.question.title
        : notification.content_deleted.title}
    </Link>
  );

  const ArticleLink = () => (
    <Link to={fullPath(`/articles/${notification.article_id}`)} class="article">
      {notification.relationships.article
        ? notification.relationships.article.title
        : notification.content_deleted.title}
    </Link>
  );

  switch (notification.type) {
    case 'question_answered':
      return (
        <Item>
          <Title>
            <UserLink /> 回答了你的提问 <QuestionLink />
          </Title>
          <Content
            content={notification.relationships.answer.content_summary}
          />
        </Item>
      );

    case 'question_commented':
      return (
        <Item>
          <Title>
            <UserLink /> 评论了你的提问 <QuestionLink />
          </Title>
          <Content
            content={notification.relationships.comment.content_summary}
          />
        </Item>
      );

    case 'question_deleted':
      return (
        <Item>
          <Title>
            你的提问{' '}
            <span class="question deleted">
              {notification.content_deleted.title}
            </span>{' '}
            已被删除
          </Title>
          <Content content={notification.content_deleted.content_rendered} />
        </Item>
      );

    case 'article_commented':
      return (
        <Item>
          <Title>
            <UserLink /> 评论了你的文章 <ArticleLink />
          </Title>
          <Content
            content={notification.relationships.comment.content_summary}
          />
        </Item>
      );

    case 'article_deleted':
      return (
        <Item>
          <Title>
            你的文章{' '}
            <span class="article deleted">
              {notification.content_deleted.title}
            </span>{' '}
            已被删除
          </Title>
          <Content content={notification.content_deleted.content_rendered} />
        </Item>
      );

    case 'answer_commented':
      return (
        <Item>
          <Title>
            <UserLink /> 评论了你在提问 <QuestionLink /> 中的回答
          </Title>
          <Content
            content={notification.relationships.comment.content_summary}
          />
        </Item>
      );

    case 'answer_deleted':
      return (
        <Item>
          <Title>
            你在提问 <QuestionLink /> 中的回答已被删除
          </Title>
          <Content content={notification.content_deleted.content_rendered} />
        </Item>
      );

    case 'comment_replied':
      if (notification.answer_id && notification.question_id) {
        return (
          <ReplyWrapper>
            <Title>
              <UserLink /> 回复了你在提问 <QuestionLink /> 的回答中的评论
            </Title>
          </ReplyWrapper>
        );
      }

      if (notification.question_id) {
        return (
          <ReplyWrapper>
            <Title>
              <UserLink /> 回复了你在提问 <QuestionLink /> 中的评论
            </Title>
          </ReplyWrapper>
        );
      }

      if (notification.article_id) {
        return (
          <ReplyWrapper>
            <Title>
              <UserLink /> 回复了你在文章 <ArticleLink /> 中的评论
            </Title>
          </ReplyWrapper>
        );
      }

      return null;

    case 'comment_deleted':
      if (notification.answer_id && notification.question_id) {
        return (
          <CommentDeletedWrapper>
            <Title>
              你在提问 <QuestionLink /> 的回答中的评论已被删除
            </Title>
          </CommentDeletedWrapper>
        );
      }

      if (notification.question_id) {
        return (
          <CommentDeletedWrapper>
            <Title>
              你在提问 <QuestionLink /> 中的评论已被删除
            </Title>
          </CommentDeletedWrapper>
        );
      }

      if (notification.article_id) {
        return (
          <CommentDeletedWrapper>
            <Title>
              你在文章 <ArticleLink /> 中的评论已被删除
            </Title>
          </CommentDeletedWrapper>
        );
      }

      return null;

    default:
      return null;
  }
};
