import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import $ from 'mdui.JQ';
import './index.less';

import Loading from '../../components/loading';
import Loaded from '../../components/loaded';
import Empty from '../../components/empty';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.users_dialog;
  const state = global_state.components.users_dialog;

  const isEmpty =
    !state.loading &&
    !state.data.length &&
    state.pagination;

  const isLoaded =
    !state.loading &&
    state.pagination &&
    state.pagination.page === state.pagination.total_page;

  const isLoading = state.loading;

  return (
    <div
      class="mdui-dialog mc-users-dialog"
      oncreate={ element => actions.init(element) }
    >

      <div class="mdui-dialog-title">
        <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close><i class="mdui-icon material-icons">close</i></button>
        {state.pagination && (state.type === 'following' ? `关注了 ${state.pagination.total} 人` : `${state.pagination.total} 位关注者`)}
      </div>

      <div class="mdui-dialog-content">

        {/* 用户列表 */}
        <ul class="mdui-list">{
          state.data.map(item => (
            <li
              class="mdui-list-item mdui-ripple"
              key={item.user_id}
              onclick={() => {
                actions.close();
                location.actions.go($.path(`/users/${item.user_id}`));
              }}
            >
              <div class="mdui-list-item-avatar">
                <img src={item.avatar.m}/>
              </div>
              <div class="mdui-list-item-content">{item.username}</div>
            </li>
          ))
        }</ul>

        {/* 空状态 */}
        {state.type === 'following' ? <Empty
          show={isEmpty}
          title="尚未关注任何用户"
          description="关注用户后，相应用户就会显示在此处。"
        /> : ''}

        {state.type === 'followers' ? <Empty
          show={isEmpty}
          title="没有任何关注者"
          description="用户关注了你后，相应用户就会显示在此处。"
        /> : ''}

        {/* 加载状态 */}
        <Loading show={isLoading}/>
        <Loaded show={isLoaded}/>

      </div>
    </div>
  );
};
