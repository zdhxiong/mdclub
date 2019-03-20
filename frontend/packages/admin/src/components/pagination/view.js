import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

const OrderItem = ({ order, currentOrder, changeOrder, afterChangeOrder }) => (
  <li class="mdui-menu-item">
    <a href="" onclick={e => changeOrder({ e, order: order.value, onChange: afterChangeOrder })}>
      <i class="mdui-menu-item-icon mdui-icon material-icons">
        {order.value === currentOrder ? 'check' : ''}
      </i> {order.name}
    </a>
  </li>
);

export default ({
                  onChange,
                  loading,
                  orders,
                  order,
                  changeOrder,
                  afterChangeOrder,
}) => (global_state, global_actions) => {
  const state = global_state.components.pagination;
  const actions = global_actions.components.pagination;

  return () => (
    <div
      class="mc-pagination"
      ondestroy={actions.destroy}
    >
      <button
        class="prev mdui-btn mdui-btn-icon"
        title={(!state.previous || loading) ? '' : '上一页'}
        disabled={!state.previous || loading}
        onclick={() => actions.toPrevPage(onChange)}
      >
        <i class="mdui-icon material-icons">chevron_left</i>
      </button>
      <button
        class="next mdui-btn mdui-btn-icon"
        title={(!state.next || loading) ? '' : '下一页'}
        disabled={!state.next || loading}
        onclick={() => actions.toNextPage(onChange)}
      >
        <i class="mdui-icon material-icons">chevron_right</i>
      </button>
      <button
        id="pagination-setting-menu-trigger"
        class="mdui-btn mdui-btn-icon"
        mdui-tooltip={'{content: \'分页设置\', delay: 300}'}
        mdui-menu="{target: '#pagination-setting-menu', covered: false}"
      >
        <i class="mdui-icon material-icons">more_vert</i>
      </button>
      <div class="mdui-menu menu" id="pagination-setting-menu">
        <div class="label">每页显示行数</div>
        <div class="mdui-btn-group">
          <button
            type="button"
            class={cc([
              'mdui-btn',
              { 'mdui-btn-active': state.per_page === 10 },
            ])}
            onclick={e => actions.onPerPageChange({ e, per_page: 10, onChange })}
          >10</button>
          <button
            type="button"
            class={cc([
              'mdui-btn',
              { 'mdui-btn-active': state.per_page === 25 },
            ])}
            onclick={e => actions.onPerPageChange({ e, per_page: 25, onChange })}
          >25</button>
          <button
            type="button"
            class={cc([
              'mdui-btn',
              { 'mdui-btn-active': state.per_page === 50 },
            ])}
            onclick={e => actions.onPerPageChange({ e, per_page: 50, onChange })}
          >50</button>
          <button
            type="button"
            class={cc([
              'mdui-btn',
              { 'mdui-btn-active': state.per_page === 100 },
            ])}
            onclick={e => actions.onPerPageChange({ e, per_page: 100, onChange })}
          >100</button>
        </div>
        <If condition={orders.length}>
          <div class="divider"></div>
          <div class="label">排序方式</div>
          {orders.map(_order => <OrderItem
            order={_order}
            currentOrder={order}
            changeOrder={changeOrder}
            afterChangeOrder={afterChangeOrder}
          />)}
        </If>
      </div>
    </div>
  );
};
