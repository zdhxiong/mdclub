import { h } from 'hyperapp';
import cc from 'classcat';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../elements/loading';
import Empty from '../../elements/empty';
import Pagination from '../../components/pagination/view';

const CheckAll = ({ isChecked, onChange }) => (
  <th class="mdui-table-cell-checkbox">
    <label class="mdui-checkbox">
      <input type="checkbox" checked={isChecked} onchange={onChange}/>
      <i class="mdui-checkbox-icon"></i>
    </label>
  </th>
);

const CheckOne = ({ isChecked, onChange, avatar = false }) => (
  <td class={cc([
    'mdui-table-cell-checkbox',
    { 'with-avatar': avatar },
  ])}>
    <If condition={avatar}>
      <img class="avatar" src={avatar}/>
    </If>
    <label class="mdui-checkbox">
      <input type="checkbox" checked={isChecked} onchange={onChange}/>
      <i class="mdui-checkbox-icon"></i>
    </label>
  </td>
);

const ActionBtn = ({ icon, label, href, onClick }) => (
  <a
    class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon"
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

const ColumnTdText = ({ column, row }) => {
  /* eslint-disable no-eval */
  const value = eval(`row.${column.field}`);

  switch (column.type) {
    case 'time':
      return <span title={timeHelper.format(value)}>{timeHelper.friendly(value)}</span>;

    case 'relation':
      return <a onclick={e => column.onClick({ e, row })}>{value}</a>;

    case 'handler':
      return column.handler(row);

    default:
      return value;
  }
};

const ColumnTD = ({ column, row }) => {
  const style = column.width && `width: ${column.width}px;`;

  switch (column.type) {
    case 'number':
      return (
        <td class="mdui-table-col-numeric" style={style}>
          <ColumnTdText column={column} row={row}/>
        </td>
      );

    default:
      return (
        <td style={style}>
          <ColumnTdText column={column} row={row}/>
        </td>
      );
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
  const state = global_state.components.datatable;
  const actions = global_actions.components.datatable;

  const isEmpty = !state.loading && !state.data.length;
  const isLoading = state.loading;

  return () => (
    <div
      class="mc-datatable"
      oncreate={element => actions.init({ element, global_actions })}
      ondestroy={actions.destroy}
    >
      <table class="mdui-table">
        <thead>
          <tr class={cc([{ checked: state.checkedCount }])}>
            <CheckAll
              isChecked={state.isCheckedAll}
              onChange={e => actions.checkAll(e)}
            />
            <If condition={state.checkedCount}>
              <ColumnTHChecked state={state}/>
            </If>
            <If condition={!state.checkedCount}>
              {state.columns.map((column, index) => {
                if (index !== state.columns.length - 1) {
                  return <ColumnTH column={column}/>;
                }
                return null;
              })}
              <th class="actions" style={state.columns.length && `width: ${state.columns[state.columns.length - 1].width}px`}>
                <Pagination
                  onChange={loadData}
                  loading={state.loading}
                  orders={state.orders}
                  order={state.order}
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
                  state.onRowClick(row);
                }
              }}
            >
              <CheckOne
                isChecked={state.isCheckedRows[row[state.primaryKey]]}
                onChange={() => actions.checkOne(row[state.primaryKey])}
                avatar={row.avatar ? row.avatar.s : false}
              />
              {state.columns.map((column, index) => {
                if (index !== state.columns.length - 1) {
                  return <ColumnTD column={column} row={row}/>;
                }
                return null;
              })}
              <td class="actions" style={`width: ${state.columns[state.columns.length - 1].width}px;`}>
                <span class="placeholder">
                  <ColumnTdText column={state.columns[state.columns.length - 1]} row={row}/>
                </span>
                {state.buttons.map(button => (
                  <ColumnTDAction button={button} row={row}/>
                ))}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
