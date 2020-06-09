import { h } from 'hyperapp';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Item from '~/components/topic-item/view.jsx';

export default (state, actions) => ({ match }) => (
  <div
    oncreate={actions.onCreate}
    ondestroy={actions.onDestroy}
    key={match.url}
    id="page-topics"
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
          <If condition={!isLoading && state.following_data.length}>
            <div class="subheading mdui-text-color-theme-secondary">
              <If condition={tabName === 'following'}>
                已关注 {state[`${tabName}_pagination`].total} 个话题
              </If>
              <If condition={tabName === 'recommended'}>话题推荐</If>
            </div>
          </If>
          <div class="items-wrapper">
            {state[`${tabName}_data`].map((topic) => (
              <Item topic={topic} actions={actions} />
            ))}
          </div>

          <If condition={tabName === 'following'}>
            <Empty
              show={isEmpty}
              title="尚未关注任何话题"
              description="关注话题后，相应话题就会显示在此处。"
              action={() => {
                actions.toRecommended();
              }}
              action_text="查看精选话题"
            />
          </If>

          <If condition={tabName === 'recommended'}>
            <Empty
              show={isEmpty}
              title="管理员未发布任何话题"
              description="待管理员发布话题后，会显示在此处"
            />
          </If>

          <Loading show={isLoading} />
          <Loaded show={isLoaded} />
        </div>
      );
    })}
  </div>
);
