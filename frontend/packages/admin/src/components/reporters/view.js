import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import './index.less';

import Loading from '../../elements/loading';
import Loaded from '../../elements/loaded';
import Empty from '../../elements/empty';

const Item = ({ item }) => (
  <li class="mdui-list-item" key={item.report_id}>
    <div class="mdui-list-item-avatar">
      <img src={item.relationship.reporter.avatar.s}/>
    </div>
    <div class="mdui-list-item-content">
      <div class="mdui-list-item-title">{item.relationship.reporter.username}</div>
      <div class="mdui-list-item-text">{item.reason}</div>
    </div>
  </li>
);

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.reporters;
  const state = global_state.components.reporters;
  const { data, pagination } = state;

  const isLoading = state.loading;
  const isEmpty = !isLoading && !data.length && pagination;
  const isLoaded = !isLoading && pagination && pagination.page === pagination.pages;

  return (
    <div class="mdui-dialog mc-reporters" oncreate={ element => actions.init(element) }>
      <div class="mdui-dialog-title">
        <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close>
          <i class="mdui-icon material-icons">close</i>
        </button>
        <If condition={pagination}>{`${pagination.total} 人举报`}</If>
      </div>
      <div class="mdui-dialog-content">
        <ul class="mdui-list">
          {data.map(item => <Item item={item}/>)}
        </ul>
        <If condition={isEmpty}><Empty/></If>
        <If condition={isLoading}><Loading/></If>
        <If condition={isLoaded}><Loaded/></If>
      </div>
    </div>
  );
};
