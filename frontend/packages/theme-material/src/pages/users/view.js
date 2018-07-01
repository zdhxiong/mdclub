import { h } from 'hyperapp';
import './index.less';

import Loading from '../../components/loading';
import Loaded from '../../components/loaded';
import Empty from '../../components/empty';

import Item from './components/item';

export default (global_state, global_actions) => {
  const actions = global_actions.users;
  const state = global_state.users;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
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
          state[`${tabName}_pagination`].page === state[`${tabName}_pagination`].total_page;

        const isLoading = state[`${tabName}_loading`];

        return () => (
          <div id={tabName}>
            {!isLoading && state[`${tabName}_data`].length ? <div class="subheading">
              {tabName === 'following' ? `已关注 ${state[`${tabName}_pagination`].total}` : ''}
              {tabName === 'followers' ? `关注者 ${state[`${tabName}_pagination`].total}` : ''}
              {tabName === 'recommended' ? '人员推荐' : ''}
            </div> : ''}

            <div class="mdui-row-xs-2 mdui-row-sm-3 mdui-row-md-4">
              {state[`${tabName}_data`].map(user => (
                <Item user={user}/>
              ))}
            </div>

            {tabName === 'following' ? <Empty
              show={isEmpty}
              title="尚未关注任何用户"
              description="关注用户后，相应用户就会显示在此处。"
              action={() => { actions.toRecommended(); }}
              action_text="查看推荐用户"
            /> : ''}

            {tabName === 'followers' ? <Empty
              show={isEmpty}
              title="你还没有关注者"
              description="用户关注了你后，相应用户就会显示在此处。"
            /> : ''}

            {tabName === 'recommended' ? <Empty
              show={isEmpty}
              title="已关注所有用户"
              description="你未关注的用户会显示再此处。"
            /> : ''}

            <Loading show={isLoading}/>
            <Loaded show={isLoaded}/>
          </div>
        );
      })}
    </div>
  );
};
