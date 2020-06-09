import { h } from 'hyperapp';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Item from './components/item/view.jsx';

export default (state, actions) => ({ match }) => (
  <div
    oncreate={actions.onCreate}
    ondestroy={actions.onDestroy}
    key={match.url}
    id="page-users"
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
          <If condition={!isLoading && state[`${tabName}_data`].length}>
            <div class="subheading mdui-text-color-theme-secondary">
              <If condition={tabName === 'followees'}>
                已关注 {state[`${tabName}_pagination`].total} 人
              </If>
              <If condition={tabName === 'followers'}>
                关注者 {state[`${tabName}_pagination`].total} 人
              </If>
              <If condition={tabName === 'recommended'}>人员推荐</If>
            </div>
          </If>

          <div class="items-wrapper">
            {state[`${tabName}_data`].map((user) => (
              <Item user={user} actions={actions} />
            ))}
          </div>

          <If condition={tabName === 'followees'}>
            <Empty
              show={isEmpty}
              title="尚未关注任何用户"
              description="关注用户后，相应用户就会显示在此处。"
              action={() => {
                actions.toRecommended();
              }}
              action_text="查看推荐用户"
            />
          </If>

          <If condition={tabName === 'followers'}>
            <Empty
              show={isEmpty}
              title="你还没有关注者"
              description="用户关注了你后，相应用户就会显示在此处。"
            />
          </If>

          <If condition={tabName === 'recommended'}>
            <Empty
              show={isEmpty}
              title="已关注所有用户"
              description="你未关注的用户会显示再此处。"
            />
          </If>

          <Loading show={isLoading} />
          <Loaded show={isLoaded} />
        </div>
      );
    })}
  </div>
);
