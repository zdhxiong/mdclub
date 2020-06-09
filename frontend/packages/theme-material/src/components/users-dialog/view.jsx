import { h } from 'hyperapp';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Follow from '~/components/follow/view.jsx';

const Title = ({ state }) => (
  <div class="mdui-dialog-title">
    <button class="close mdui-btn mdui-btn-icon mdui-ripple" mdui-dialog-close>
      <i class="mdui-icon material-icons">close</i>
    </button>
    <If condition={state.pagination}>
      <Choose>
        <When condition={state.type === 'followees'}>
          关注了 {state.pagination.total} 人
        </When>
        <Otherwise>{state.pagination.total} 位关注者</Otherwise>
      </Choose>
    </If>
  </div>
);

const Item = ({ item, actions }) => (
  <div class="item">
    <div
      class="mdui-list-item mdui-ripple"
      key={item.user_id}
      onclick={() => actions.onItemClick(item.user_id)}
    >
      <div class="mdui-list-item-avatar">
        <img src={item.avatar.middle} />
      </div>
      <div class="mdui-list-item-content">{item.username}</div>
    </div>
    <If condition={!item.relationships.is_me}>
      <Follow
        item={item}
        type="users_dialog"
        id={item.user_id}
        actions={actions}
      />
    </If>
  </div>
);

export default ({ state, actions }) => {
  const isEmpty = !state.loading && !state.data.length && state.pagination;
  const isLoading = state.loading;
  const isLoaded =
    !state.loading &&
    state.pagination &&
    state.pagination.page === state.pagination.pages;

  return (
    <div
      key="users-dialog"
      class="mdui-dialog mc-users-dialog"
      oncreate={(element) => actions.onCreate({ element })}
    >
      <Title state={state} />
      <div class="mdui-dialog-content">
        <div class="mdui-list">
          {state.data.map((item) => (
            <Item item={item} actions={actions} />
          ))}
        </div>

        <Empty
          show={isEmpty}
          title={
            state.type === 'followees' ? '尚未关注任何用户' : '没有任何关注者'
          }
        />
        <Loading show={isLoading} />
        <Loaded show={isLoaded} />
      </div>
    </div>
  );
};
