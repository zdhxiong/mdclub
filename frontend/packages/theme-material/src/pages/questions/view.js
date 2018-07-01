import { h } from 'hyperapp';
import './index.less';

import Loading from '../../components/loading';
import Loaded from '../../components/loaded';
import Empty from '../../components/empty';
import EditorDialog from '../../components/editor-dialog';

import Item from './components/item';

export default (global_state, global_actions) => {
  const actions = global_actions.questions;
  const state = global_state.questions;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-questions"
      class="mdui-container"
    >
      {state.tabs.map((tabName) => {
        const isEmpty =
          !state[`${tabName}_loading`] &&
          !state[`${tabName}_data`].length &&
          state[`${tabName}_pagination`];

        const isLoaded =
          !state[`${tabName}_loading`] &&
          state[`${tabName}_pagination`] &&
          state[`${tabName}_pagination`].page === state[`${tabName}_pagination`].total_page;

        const isLoading = state[`${tabName}_loading`];

        return () => (
          <div id={tabName}>

            <div class="item-list">
              {state[`${tabName}_data`].map(question => (
                <Item question={question}/>
              ))}
            </div>

            {tabName === 'recent' ? <Empty
              show={isEmpty}
              title="尚未发布任何问题"
              description="此处会显示最近更新的问题。"
              action={() => { actions.openEditor(); }}
              action_text="发布问题"
            /> : ''}

            {tabName === 'popular' ? <Empty
              show={isEmpty}
              title="没有近期热门问题"
              description="此处会显示近期最受欢迎的问题。"
              action={() => { actions.toRecent(); }}
              action_text="查看最新问题"
            /> : ''}

            {tabName === 'following' ? <Empty
              show={isEmpty}
              title="尚未关注任何问题"
              description="关注问题后，相应问题就会显示在此处。"
              action={() => { actions.toPopular(); }}
              action_text="查看热门问题"
            /> : ''}

            <Loading show={isLoading}/>
            <Loaded show={isLoaded}/>
          </div>
        );
      })}

      <button
        class="mdui-fab mdui-fab-fixed mdui-color-theme-accent mdui-ripple"
        onclick={actions.openEditor}
        mdui-tooltip="{content: '提问题'}"
      >
        <i class="mdui-icon material-icons">add</i>
      </button>

      <EditorDialog
        id="page-questions-editor"
        autoSaveKey="question-content"
        onsubmit={(Editor) => {
          actions.publish(Editor);
        }}
        submitting={state.publishing}
        onClearDrafts={actions.clearDrafts}
        titlePlaceholder="问题标题"
        contentPlaceholder="问题详细信息"
      ></EditorDialog>
    </div>
  );
};
