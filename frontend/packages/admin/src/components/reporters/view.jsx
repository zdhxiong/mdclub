import { h } from 'hyperapp';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';

const Item = ({ item }) => (
  <li class="mdui-list-item" key={item.report_id}>
    <div class="mdui-list-item-avatar">
      <img src={item.relationships.reporter.avatar.small} />
    </div>
    <div class="mdui-list-item-content">
      <div class="mdui-list-item-title">
        {item.relationships.reporter.username}
      </div>
      <div class="mdui-list-item-text">{item.reason}</div>
    </div>
  </li>
);

export default ({ state, actions }) => {
  const { data, pagination, loading } = state;

  const isEmpty = !loading && !data.length && pagination;
  const isLoaded =
    !loading && pagination && pagination.page === pagination.pages;

  return (
    <div
      class="mdui-dialog mc-reporters"
      oncreate={(element) => actions.onCreate({ element })}
    >
      <div class="mdui-dialog-title">
        <button
          class="mdui-btn mdui-btn-icon mdui-ripple close"
          mdui-dialog-close
        >
          <i class="mdui-icon material-icons">close</i>
        </button>
        <If condition={pagination}>{`${pagination.total} 人举报`}</If>
      </div>
      <div class="mdui-dialog-content">
        <ul class="mdui-list">
          {data.map((item) => (
            <Item item={item} />
          ))}
        </ul>
        <Empty show={isEmpty} />
        <Loading show={loading} />
        <Loaded show={isLoaded} />
      </div>
    </div>
  );
};
