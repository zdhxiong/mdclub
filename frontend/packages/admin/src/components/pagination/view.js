import { h } from 'hyperapp';
import './index.less';

const PrevPage = ({ state, loading, onClick }) => (
  <button
    class="prev mdui-btn mdui-btn-icon"
    title="上一页"
    disabled={!state.previous || loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_left</i>
  </button>
);

const NextPage = ({ state, loading, onClick }) => (
  <button
    class="next mdui-btn mdui-btn-icon"
    title="下一页"
    disabled={!state.next || loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_right</i>
  </button>
);

const SettingDivider = () => (
  <li class="mdui-divider"></li>
);

const SettingLabel = ({ label }) => (
  <li class="mdui-menu-item" disabled>
    <a href="" onclick={e => e.preventDefault()}>{label}</a>
  </li>
);

const PerPageItem = ({ perPage, num, onClick }) => (
  <li class="mdui-menu-item">
    <a href="" onclick={onClick}>
      <i class="mdui-menu-item-icon mdui-icon material-icons">{perPage === num ? 'check' : ''}</i> {num}
    </a>
  </li>
);

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
      <PrevPage
        state={state}
        loading={loading}
        onClick={() => actions.toPrevPage(onChange)}
      />
      <NextPage
        state={state}
        loading={loading}
        onClick={() => actions.toNextPage(onChange)}
      />
      <button
        class="mdui-btn mdui-btn-icon"
        mdui-tooltip={'{content: \'分页设置\', delay: 300}'}
        mdui-menu="{target: '#pagination-setting-menu', covered: false}"
      >
        <i class="mdui-icon material-icons">more_vert</i>
      </button>
      <ul class="mdui-menu mdui-menu-cascade" id="pagination-setting-menu">
        <If condition={orders.length}>
          <SettingLabel label="排序方式"/>
          {orders.map(_order => <OrderItem
            order={_order}
            currentOrder={order}
            changeOrder={changeOrder}
            afterChangeOrder={afterChangeOrder}
          />)}
          <SettingDivider/>
        </If>
        <SettingLabel label={`第 ${state.page} 页，共 ${state.pages} 页`}/>
        <SettingDivider/>
        <SettingLabel label="每页显示行数"/>
        <PerPageItem
          perPage={state.per_page}
          num={10}
          onClick={e => actions.onPerPageChange({ e, num: 10, onChange })}
        />
        <PerPageItem
          perPage={state.per_page}
          num={20}
          onClick={e => actions.onPerPageChange({ e, num: 20, onChange })}
        />
        <PerPageItem
          perPage={state.per_page}
          num={40}
          onClick={e => actions.onPerPageChange({ e, num: 40, onChange })}
        />
        <PerPageItem
          perPage={state.per_page}
          num={100}
          onClick={e => actions.onPerPageChange({ e, num: 100, onChange })}
        />
      </ul>
    </div>
  );
};
