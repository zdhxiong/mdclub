import { h } from 'hyperapp';
import cc from 'classcat';
import mdui from 'mdui';
import $ from 'mdui.jq';
import './index.less';

export default ({
  onChange,
  loading,
  orders,
  order,
  changeOrder,
  afterChangeOrder,
}) => ({ pagination: state }, { pagination: actions }) => {
  const PerPageItem = ({ per_page }) => (
    <button
      type="button"
      class={cc([
        'mdui-btn',
        'mdui-text-color-theme-text',
        { 'mdui-btn-active': state.per_page === per_page },
      ])}
      onclick={(e) => {
        actions.onPerPageChange({ e, per_page, onChange });
      }}
    >
      {per_page}
    </button>
  );

  const OrderItem = ({ toOrder }) => (
    <li class="mdui-menu-item">
      <a
        href=""
        onclick={(e) =>
          changeOrder({ e, order: toOrder.value, onChange: afterChangeOrder })
        }
      >
        <i class="mdui-menu-item-icon mdui-icon material-icons">
          {toOrder.value === order ? 'check' : ''}
        </i>
        {toOrder.name}
      </a>
    </li>
  );

  return (
    <div class="mc-pagination" ondestroy={actions.onDestroy}>
      <span class="label mdui-text-color-theme-secondary">
        第 {(state.page - 1) * state.per_page + 1} -{' '}
        {state.page * state.per_page} 行， 共 {state.total} 行
      </span>
      <button
        class="prev mdui-btn mdui-btn-icon"
        title={!state.previous || loading ? '' : '上一页'}
        disabled={!state.previous || loading}
        onclick={() => actions.toPrevPage(onChange)}
      >
        <i class="mdui-icon material-icons">chevron_left</i>
      </button>
      <button
        class="next mdui-btn mdui-btn-icon"
        title={!state.next || loading ? '' : '下一页'}
        disabled={!state.next || loading}
        onclick={() => actions.toNextPage(onChange)}
      >
        <i class="mdui-icon material-icons">chevron_right</i>
      </button>
      <button
        id="pagination-setting-menu-trigger"
        class="mdui-btn mdui-btn-icon"
        mdui-tooltip={"{content: '分页及排序设置', delay: 300}"}
        oncreate={(element) => {
          const menu = new mdui.Menu(element, '#pagination-setting-menu', {
            covered: false,
          });

          $(element).data('menu-instance', menu);
        }}
      >
        <i class="mdui-icon material-icons">more_vert</i>
      </button>
      <div class="mdui-menu menu" id="pagination-setting-menu">
        <div class="label mdui-text-color-theme-secondary">每页显示行数</div>
        <div class="mdui-btn-group">
          <PerPageItem per_page={10} />
          <PerPageItem per_page={25} />
          <PerPageItem per_page={50} />
          <PerPageItem per_page={100} />
        </div>
        <If condition={orders.length}>
          <div class="mdui-divider" />
          <div class="label mdui-text-color-theme-secondary">排序方式</div>
          {orders.map((toOrder) => (
            <OrderItem toOrder={toOrder} />
          ))}
        </If>
      </div>
    </div>
  );
};
