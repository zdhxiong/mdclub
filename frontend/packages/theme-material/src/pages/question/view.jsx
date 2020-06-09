import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from 'hyperapp-router';
import { isUndefined } from 'mdui.jq/es/utils';
import { richText, summaryText } from '~/utils/html';
import { emit } from '~/utils/pubsub';
import { fullPath } from '~/utils/path';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import UserLine from '~/components/user-line/view.jsx';
import Follow from '~/components/follow/view.jsx';
import Editor from '~/components/editor/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';
import TopicsBar from '~/components/topics-bar/view.jsx';
import ListHeader from '~/components/list-header/view.jsx';
import Nav from '~/components/nav/view.jsx';
import AnswerItem from './components/answer-item/view.jsx';
import CommentButton from './components/comment-button/view.jsx';

export default (state, actions) => ({ match }) => {
  // 判断当前是提问详情页还是回答详情页
  let answer_id = 0;
  const route = isUndefined(match.params.answer_id) ? 'question' : 'answer';

  if (route === 'answer') {
    answer_id = parseInt(match.params.answer_id, 10);
  }

  const question_id = parseInt(match.params.question_id, 10);
  const {
    question,
    loading,
    answer_data,
    answer_pagination,
    answer_loading,
  } = state;

  let answer_count = 0;
  if (question) {
    answer_count = question.answer_count;
  }
  if (answer_pagination) {
    answer_count = answer_pagination.total;
  }

  const isEmpty =
    !answer_loading && answer_pagination && !answer_pagination.total;

  const AllAnswersLink = ({ count, url }) => (
    <Link class="all-answers mdui-text-color-theme-text" to={fullPath(url)}>
      查看全部 {count} 个回答
    </Link>
  );

  return (
    <div
      oncreate={() => actions.onCreate({ question_id, answer_id, route })}
      ondestroy={actions.onDestroy}
      key={match.url}
      id="page-question"
      class="mdui-container"
    >
      <Nav path={answer_id ? `/questions/${question_id}` : '/questions'} />
      <div class="mdui-card mdui-card-shadow question">
        <If condition={question}>
          <h1
            class="title"
            oncreate={summaryText(question.title)}
            onupdate={summaryText(question.title)}
          />
          <UserLine
            actions={actions}
            user={question.relationships.user}
            time={question.create_time}
            dataName="question"
          />
          <div
            class="mdui-typo content"
            oncreate={richText(question.content_rendered)}
            onupdate={richText(question.content_rendered)}
          />
          <If condition={question.relationships.topics.length}>
            <TopicsBar topics={question.relationships.topics} />
          </If>
          <div class="actions">
            <Follow item={question} type="question" actions={actions} />
            <CommentButton
              item={question}
              onClick={() => {
                emit('comments_dialog_open', {
                  commentable_type: 'question',
                  commentable_id: question_id,
                });
              }}
            />
            <div class="flex-grow" />
            <OptionsButton
              type="question"
              item={question}
              extraOptions={[
                {
                  name: `查看 ${question.follower_count} 位关注者`,
                  onClick: () => {
                    emit('users_dialog_open', {
                      type: 'question_followers',
                      id: question_id,
                    });
                  },
                },
              ]}
            />
          </div>
        </If>
        <Loading show={loading} />
      </div>

      <If condition={route === 'question'}>
        <Empty show={isEmpty} title="尚未有人回答该问题" />

        <ListHeader
          show={!isEmpty}
          title={`共 ${answer_count} 个回答`}
          disabled={answer_loading}
          currentOrder={state.answer_order}
          orders={[
            {
              order: '-vote_count',
              name: '最热门',
            },
            {
              order: 'create_time',
              name: '发布时间（从早到晚）',
            },
            {
              order: '-create_time',
              name: '发布时间（从晚到早）',
            },
          ]}
          onChangeOrder={actions.changeOrder}
        />

        <If condition={answer_data && answer_data.length}>
          <div class="mdui-card answers">
            {answer_data.map((answer) => (
              <AnswerItem answer={answer} actions={actions} />
            ))}
          </div>
        </If>

        <Loaded
          show={
            !answer_loading &&
            answer_pagination &&
            answer_pagination.page === answer_pagination.pages
          }
        />
      </If>

      <If condition={route === 'answer' && question}>
        <AllAnswersLink
          count={question.answer_count}
          url={`/questions/${question_id}`}
        />
        <div class="mdui-card answers">
          <AnswerItem answer={answer_data[0]} actions={actions} />
        </div>
        <AllAnswersLink
          count={question.answer_count}
          url={`/questions/${question_id}`}
        />
      </If>

      <Loading show={answer_loading} />

      <button
        class={cc([
          'mdui-fab',
          'mdui-fab-fixed',
          'mdui-fab-extended',
          'mdui-ripple',
          'mdui-color-theme',
          {
            'mdui-hidden': state.editor_open,
          },
        ])}
        onclick={actions.editorOpen}
      >
        <i class="mdui-icon material-icons">add</i>
        <span>写回答</span>
      </button>

      <Editor
        id="page-question-editor"
        title="写回答"
        withTitle={false}
        withTopics={false}
        onSubmit={actions.publishAnswer}
        publishing={state.answer_publishing}
        state={state}
        actions={actions}
      />
    </div>
  );
};
