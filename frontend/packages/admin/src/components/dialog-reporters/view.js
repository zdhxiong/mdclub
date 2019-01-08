import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import './index.less';

import Loading from '../../elements/loading';
import Loaded from '../../elements/loaded';
import Empty from '../../elements/empty';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.dialogReporters;
  const state = global_state.components.dialogReporters;

  const isEmpty =
    !state.loading &&
    !state.data.length &&
    state.pagination;

  const isLoaded =
    !state.loading &&
    state.pagination &&
    state.pagination.page === state.pagination.pages;

  const isLoading = state.loading;

  return (
    <div
      class="mdui-dialog mc-dialog-reporters"
      oncreate={ element => actions.init(element) }
    >
      <div class="mdui-dialog-title">
        <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close>
          <i class="mdui-icon material-icons">close</i>
        </button>
        <If condition={state.pagination}>{`${state.pagination.total} 人举报`}</If>
      </div>
      <div class="mdui-dialog-content">
        <ul class="mdui-list">
          {state.data.map(item => (
            <li class="mdui-list-item" key={item.report_id}>
              <div class="mdui-list-item-avatar">
                <img src={item.relationship.reporter.avatar.s}/>
              </div>
              <div class="mdui-list-item-content">
                <div class="mdui-list-item-title">{item.relationship.reporter.username}</div>
                <div class="mdui-list-item-text">{item.reason}</div>
              </div>
            </li>
          ))}
        </ul>
        <If condition={isEmpty}><Empty/></If>
        <If condition={isLoading}><Loading/></If>
        <If condition={isLoaded}><Loaded/></If>
      </div>
    </div>
  );
};
