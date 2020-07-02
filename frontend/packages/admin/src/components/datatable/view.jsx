import { h } from 'hyperapp';
import cc from 'classcat';
import { unescape } from 'html-escaper';
import { isUndefined } from 'mdui.jq/es/utils';
import { timeFormat, timeFriendly } from '~/utils/time';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Pagination from '~/components/pagination/view.jsx';

const CheckAll = ({ isChecked, onChange }) => (
  <th class="mdui-table-cell-checkbox">
    <label class="mdui-checkbox">
      <input type="checkbox" checked={isChecked} onchange={onChange} />
      <i class="mdui-checkbox-icon" />
    </label>
  </th>
);

const CheckOne = ({ isChecked, onChange, avatar = false }) => (
  <td class={cc(['mdui-table-cell-checkbox', { 'with-avatar': avatar }])}>
    <If condition={avatar}>
      <img class="avatar" src={avatar} />
    </If>
    <label class="mdui-checkbox">
      <input type="checkbox" checked={isChecked} onchange={onChange} />
      <i class="mdui-checkbox-icon" />
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
      {state.batchButtons.map((button) => (
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

  if (isUndefined(value)) {
    return null;
  }

  switch (column.type) {
    case 'time':
      return <span title={timeFormat(value)}>{timeFriendly(value)}</span>;

    case 'relation':
      return (
        <a onclick={(e) => column.onClick({ e, row })}>{unescape(value)}</a>
      );

    case 'handler':
      return unescape(column.handler(row));

    case 'number':
      return value;

    default:
      return unescape(value);
  }
};

const ColumnTD = ({ column, row }) => {
  const style = column.width && `width: ${column.width}px;`;

  switch (column.type) {
    case 'number':
      return (
        <td class="mdui-table-col-numeric" style={style}>
          <ColumnTdText column={column} row={row} />
        </td>
      );

    default:
      return (
        <td style={style}>
          <ColumnTdText column={column} row={row} />
        </td>
      );
  }
};

const ColumnTDAction = ({ button, row }) => {
  switch (button.type) {
    case 'target':
      return (
        <ActionBtn
          icon="open_in_new"
          label="新窗口打开"
          href={button.getTargetLink(row)}
        />
      );

    case 'btn':
      return (
        <ActionBtn
          icon={button.icon}
          label={button.label}
          onClick={(e) => {
            e.preventDefault();
            button.onClick(row);
          }}
        />
      );

    default:
      return null;
  }
};

export default ({ loadData }) => (
  { datatable: state },
  { datatable: actions },
) => {
  const isEmpty = !state.loading && !state.data.length;
  const isLoading = state.loading;

  return (
    <div
      class={cc(['mc-datatable', { 'is-top': !state.isScrollTop }])}
      oncreate={(element) => actions.onCreate({ element })}
      ondestroy={(element) => actions.onDestroy({ element })}
    >
      <table class="mdui-table">
        <thead>
          <tr class={cc([{ checked: state.checkedCount }])}>
            <CheckAll
              isChecked={state.isCheckedAll}
              onChange={(e) => actions.checkAll(e)}
            />
            <If condition={state.checkedCount}>
              <ColumnTHChecked state={state} />
            </If>
            <If condition={!state.checkedCount}>
              {state.columns.map((column, index) => {
                if (index !== state.columns.length - 1) {
                  return <ColumnTH column={column} />;
                }
                return null;
              })}
              <th class="actions">
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

        <Loading show={isLoading} />
        <Empty show={isEmpty} />

        <tbody
          class={cc([
            {
              'is-loading': isLoading,
              'is-empty': isEmpty,
            },
          ])}
        >
          {state.data.map((row) => (
            <tr
              key={row[state.primaryKey]}
              data-id={row[state.primaryKey]}
              class={cc([
                {
                  'mdui-table-row-selected':
                    state.isCheckedRows[row[state.primaryKey]],
                  'last-visit': state.lastVisitId === row[state.primaryKey],
                },
              ])}
              onclick={(e) => {
                if (
                  typeof state.onRowClick === 'function' &&
                  e.target.nodeName === 'TD'
                ) {
                  state.onRowClick(row);
                }
              }}
            >
              <CheckOne
                isChecked={state.isCheckedRows[row[state.primaryKey]]}
                onChange={() => actions.checkOne(row[state.primaryKey])}
                avatar={row.avatar ? row.avatar.small : false}
              />
              {state.columns.map((column, index) => {
                if (index !== state.columns.length - 1) {
                  return <ColumnTD column={column} row={row} />;
                }
                return null;
              })}
              <td
                class="actions"
                style={`width: ${
                  state.columns[state.columns.length - 1].width
                }px;`}
              >
                <span class="placeholder">
                  <ColumnTdText
                    column={state.columns[state.columns.length - 1]}
                    row={row}
                  />
                </span>
                {state.buttons.map((button) => (
                  <ColumnTDAction button={button} row={row} />
                ))}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
