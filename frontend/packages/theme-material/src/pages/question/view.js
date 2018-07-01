import { h } from 'hyperapp';
import { Link } from '@hyperapp/router';
import cc from 'classcat';
import $ from 'mdui.JQ';
import './index.less';

import rawHtml from '../../helper/rawHtml';
import hljs from '../../helper/hljs';

import Loading from '../../components/loading';
import Loaded from '../../components/loaded';
import Empty from '../../components/empty';
import UserLine from '../../components/user-line';
import EditorDialog from '../../components/editor-dialog';

import Answer from './components/answer';

export default (global_state, global_actions) => {
  const actions = global_actions.question;
  const state = global_state.question;

  return ({ match }) => {
    const question_id = parseInt(match.params.question_id, 10);

    return (
      <div
        oncreate={element => actions.init({ element, global_actions, question_id })}
        key={match.url}
        id="page-question"
        class="mdui-container"
      >

        <div class="mdui-card mdui-center question">

          {state.question_loading || !state.question ? '' : <div>
            <h1 class="mdui-typo-title">{state.question.title}</h1>
            <UserLine user={state.question.relationship.user} time={state.question.create_time}/>
            <div
              class="mdui-typo content"
              oncreate={rawHtml(state.question.content_rendered)}
              onupdate={rawHtml(state.question.content_rendered)}
            ></div>
            <div class="actions">
              <button
                class={cc([
                  'mdui-btn',
                  'mdui-btn-raised',
                  'mdui-text-color-blue',
                  {
                    following: state.question.relationship.is_following,
                  },
                ])}
                onclick={() => { actions.toggleFollow(); }}
              >{state.question.relationship.is_following ? '已关注' : '关注'}</button>
              <button
                class="mdui-btn mdui-btn-raised mdui-color-theme-accent"
                onclick={() => { actions.openEditor(); }}
              >写回答</button>
            </div>
          </div>}

          <Loading show={state.question_loading}/>
        </div>

        <Empty
          show={!state.answer_pagination.total}
          title="尚未有人回答该问题"
        />

        {state.answer_pagination.total ? <div class="mdui-typo-headline answers-count">共 {state.answer_pagination.total} 个回答</div> : ''}

        <div class="mdui-card mdui-center answers">
          {state.answer_data.map(answer => (
            <Answer answer={answer}/>
          ))}
        </div>

        <Loading show={state.answer_loading}/>
        <Loaded show={
          !state.answer_loading &&
          state.answer_pagination &&
          state.answer_pagination.page === state.answer_pagination.total_page
        }/>

        <EditorDialog
          id="page-answer-editor"
          autoSaveKey={`answer-content-${question_id}`}
          onsubmit={(Editor) => {
            actions.publishAnswer(Editor);
          }}
          submitting={state.answer_publishing}
          onClearDrafts={actions.clearDrafts}
          withTitle={false}
          contentPlaceholder="写回答..."
        ></EditorDialog>

      </div>
    );
  };
};
