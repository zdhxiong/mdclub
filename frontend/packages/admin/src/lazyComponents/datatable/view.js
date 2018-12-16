import { h } from 'hyperapp';
import cc from 'classcat';
import timeHelper from '../../helper/time';
import rawHtml from '../../helper/rawHtml';
import './index.less';

import Loading from '../../components/loading';
import Empty from '../../components/empty';
import Pagination from '../../lazyComponents/pagination/view';

const CheckAll = ({ isChecked, onChange }) => (
  <th class="mdui-table-cell-checkbox">
    <label class="mdui-checkbox">
      <input type="checkbox" checked={isChecked} onchange={onChange}/>
      <i class="mdui-checkbox-icon"></i>
    </label>
  </th>
);

const CheckOne = ({ isChecked, onChange }) => (
  <td class="mdui-table-cell-checkbox">
    <label class="mdui-checkbox">
      <input type="checkbox" checked={isChecked} onchange={onChange}/>
      <i class="mdui-checkbox-icon"></i>
    </label>
  </td>
);

const ActionBtn = ({ icon, label, href, onClick }) => (
  <a
    class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
    mdui-tooltip={`{content: '${label}', delay: 300}`}
    href={href}
    target={href ? '_blank' : false}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">{icon}</i>
  </a>
);

const ColumnTH = ({ column }) => (
  <th
    key={column.field}
    class={cc([{ 'mdui-table-col-numeric': column.type === 'number' }])}
    style={column.width && `width: ${column.width}px;`}
  >
    {column.title}
  </th>
);

const ColumnTHChecked = ({ state }) => (
  <th colspan={state.columns.length}>
    <span>
      {state.batchButtons.map(button => (
        <ActionBtn
          label={button.label}
          icon={button.icon}
          onClick={() => {
            const items = [];
            state.data.map((item) => {
              if (state.isCheckedRows[item[state.primaryKey]]) {
                items.push(item);
              }
            });

            button.onClick(items);
          }}
        />
      ))}
    </span>
    <span class="mdui-float-right">已选择 {state.checkedCount} 个项目</span>
  </th>
);

const ColumnTHOrder = ({ order, orders, changeOrder, afterChangeOrder }) => (
  <If condition={orders.length}>
    <button
      class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
      mdui-tooltip={'{content: \'排序选项\', delay: 300}'}
      mdui-menu="{target: '#datatable-sort-menu', covered: false}"
    >
      <i class="mdui-icon material-icons">sort</i>
    </button>
    <ul class="mdui-menu" id="datatable-sort-menu">
      {orders.map(_order => (
        <li class="mdui-menu-item">
          <a href="" onclick={e => changeOrder({ e, order: _order.value, onChange: afterChangeOrder })}>
            <i class="mdui-menu-item-icon mdui-icon material-icons">
              {_order.value === order ? 'check' : ''}
            </i>
            {_order.name}
          </a>
        </li>
      ))}
    </ul>
  </If>
);

const ColumnTD = ({ column, row }) => {
  const value = eval(`row.${column.field}`);
  const style = column.width && `width: ${column.width}px;`;

  switch (column.type) {
    case 'number':
      return <td class="mdui-table-col-numeric" style={style}>{value}</td>;

    case 'time':
      return (
        <td style={style}>
          <span title={timeHelper.format(value)}>{timeHelper.friendly(value)}</span>
        </td>
      );

    case 'html':
      return <td oncreate={rawHtml(value)} onupdate={rawHtml(value)} style={style}></td>;

    case 'relation':
      return <td style={style}><a onclick={e => column.onClick({ e, row })}>{value}</a></td>;

    default:
      return <td style={style}>{value}</td>;
  }
};

const ColumnTDAction = ({ button, row }) => {
  switch (button.type) {
    case 'target':
      return <ActionBtn
        icon="open_in_new"
        label="新窗口打开"
        href={button.getTargetLink(row)}
      />;

    case 'btn':
      return <ActionBtn
        icon={button.icon}
        label={button.label}
        onClick={(e) => {
          e.preventDefault();
          button.onClick(row);
        }}
      />;

    default:
      return null;
  }
};

export default ({ loadData }) => (global_state, global_actions) => {
  const state = global_state.lazyComponents.datatable;
  const actions = global_actions.lazyComponents.datatable;

  const isEmpty = !state.loading && !state.data.length;
  const isLoading = state.loading;

  return () => (
    <div
      class="mc-datatable"
      oncreate={element => actions.init({ element, global_actions })}
      ondestroy={actions.destroy}
    >
      <table class="mdui-table mdui-table-hoverable">
        <thead>
          <tr class={cc([{ checked: state.checkedCount }])}>
            <CheckAll isChecked={state.isCheckedAll} onChange={e => actions.checkAll(e)}/>
            <If condition={state.checkedCount}>
              <ColumnTHChecked state={state}/>
            </If>
            <If condition={!state.checkedCount}>
              {state.columns.map(column => (
                <ColumnTH column={column}/>
              ))}
              <th class="actions" style="width: 172px;">
                <ColumnTHOrder
                  order={state.order}
                  orders={state.orders}
                  changeOrder={actions.changeOrder}
                  afterChangeOrder={loadData}
                />
              </th>
            </If>
          </tr>
        </thead>
        <If condition={isLoading}><Loading/></If>
        <If condition={isEmpty}><Empty/></If>
        <tbody class={cc([
          {
            'is-loading': isLoading,
            'is-empty': isEmpty,
          },
        ])}>
          {state.data.map(row => (
            <tr
              key={row[state.primaryKey]}
              class={cc([{ 'mdui-table-row-selected': state.isCheckedRows[row[state.primaryKey]] }])}
              onclick={(e) => {
                if (typeof state.onRowClick === 'function' && e.target.nodeName === 'TD') {
                  state.onRowClick(row[state.primaryKey]);
                }
              }}
            >
              <CheckOne
                isChecked={state.isCheckedRows[row[state.primaryKey]]}
                onChange={() => actions.checkOne(row[state.primaryKey])}
              />
              {state.columns.map(column => (
                <ColumnTD column={column} row={row}/>
              ))}
              <td class="actions" style="width: 172px;">
                {state.buttons.map(button => (
                  <ColumnTDAction button={button} row={row}/>
                ))}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
      <Pagination onChange={loadData} loading={state.loading}/>
    </div>
  );
};
