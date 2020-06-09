import { h } from 'hyperapp';
import currentUser from '~/utils/currentUser';
import { emit } from '~/utils/pubsub';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Item from './components/item/view.jsx';

export default (state, actions) => ({ match }) => {
  const isEmpty = !state.loading && !state.data.length && state.pagination;
  const isLoaded =
    !state.loading &&
    state.pagination &&
    state.pagination.page === state.pagination.pages;
  const isLoading = state.loading;

  return (
    <div
      oncreate={actions.onCreate}
      ondestroy={actions.onDestroy}
      key={match.url}
      id="page-notifications"
      class="mdui-container"
    >
      <If condition={currentUser()}>
        <Empty
          show={isEmpty}
          title="没有你的通知"
          description="此处会显示你所有的已读和未读通知"
        />

        <If condition={state.data.length}>
          <div class="item-list mdui-card mdui-card-shadow">
            {state.data.map((notification) => (
              <Item notification={notification} actions={actions} />
            ))}
          </div>
        </If>

        <If condition={!currentUser()}>
          <Empty
            show={true}
            title="登陆后才能查看通知"
            description="登陆后，你的通知消息将会显示在此处"
            action={() => {
              emit('login_open');
            }}
            action_text="登录"
          />
        </If>

        <Loading show={isLoading} />
        <Loaded show={isLoaded} />
      </If>
    </div>
  );
};
