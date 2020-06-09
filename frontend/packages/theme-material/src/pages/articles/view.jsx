import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Editor from '~/components/editor/view.jsx';

import { ArticleItem } from '~/components/list-item/view.jsx';
import TopicSelector from '~/components/editor/components/topic-selector/view.jsx';

export default (state, actions) => ({ match }) => (
  <div
    oncreate={actions.onCreate}
    key={match.url}
    id="page-articles"
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
        state[`${tabName}_pagination`].page ===
          state[`${tabName}_pagination`].pages;

      const isLoading = state[`${tabName}_loading`];

      return () => (
        <div id={tabName}>
          <If condition={state[`${tabName}_data`].length}>
            <div class="item-list mdui-card mdui-card-shadow">
              {state[`${tabName}_data`].map((article) => (
                <ArticleItem
                  article={article}
                  last_visit_id={state.last_visit_id}
                  tabName={tabName}
                  actions={actions}
                />
              ))}
            </div>
          </If>

          <If condition={tabName === 'recent'}>
            <Empty
              show={isEmpty}
              title="尚未发布任何文章"
              description="此处会显示最近更新的文章"
              action={() => {
                actions.editorOpen();
              }}
              action_text="写文章"
            />
          </If>

          <If condition={tabName === 'popular'}>
            <Empty
              show={isEmpty}
              title="没有近期热门文章"
              description="此处会显示近期最受欢迎的文章"
              action={() => {
                actions.toRecent();
              }}
              action_text="查看最新文章"
            />
          </If>

          <If condition={tabName === 'following'}>
            <Empty
              show={isEmpty}
              title="尚未关注任何文章"
              description="关注文章后，相应文章就会显示在此处"
              action={() => {
                actions.toPopular();
              }}
              action_text="查看热门文章"
            />
          </If>

          <Loading show={isLoading} />
          <Loaded show={isLoaded} />
        </div>
      );
    })}

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
      <span>写文章</span>
    </button>

    <Editor
      id="page-article-editor"
      title="新文章"
      withTitle={true}
      withTopics={true}
      onSubmit={actions.publish}
      publishing={state.publishing}
      state={state}
      actions={actions}
    />

    <TopicSelector state={state} actions={actions} />
  </div>
);
