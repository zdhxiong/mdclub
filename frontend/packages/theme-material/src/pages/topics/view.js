import { h } from 'hyperapp';
import './index.less';

import Loading from '../../components/loading';
import Loaded from '../../components/loaded';
import Empty from '../../components/empty';

import Item from './components/item';

export default (global_state, global_actions) => {
  const actions = global_actions.topics;
  const state = global_state.topics;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
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
          state[`${tabName}_pagination`].page === state[`${tabName}_pagination`].total_page;

        const isLoading = state[`${tabName}_loading`];

        return global_state.user.user.user_id || tabName === 'recommended' ? (
          <div id={tabName}>
            <div class="mdui-row-xs-2 mdui-row-sm-3 mdui-row-md-4">
              {state[`${tabName}_data`].map(topic => (
                <Item topic={topic}/>
              ))}
            </div>

            {tabName === 'following' ? <Empty
              show={isEmpty}
              title="尚未关注任何话题"
              description="关注话题后，相应话题就会显示在此处。"
              action={() => { actions.toRecommended(); }}
              action_text="查看精选话题"
            /> : ''}

            {tabName === 'recommended' ? <Empty
              show={isEmpty}
              title="已关注所有精选话题"
              description="你未关注的精选话题会显示在此处"
            /> : ''}

            <Loading show={isLoading}/>
            <Loaded show={isLoaded}/>
          </div>
        ) : '';
      })}
    </div>
  );
};
