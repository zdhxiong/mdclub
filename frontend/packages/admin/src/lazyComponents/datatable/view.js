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

const ValueNumber = ({ value }) => (
  <td class="mdui-table-col-numeric">{value}</td>
);

const ValueTime = ({ value }) => (
  <td><span title={timeHelper.format(value)}>{timeHelper.friendly(value)}</span></td>
);

const ValueHtml = ({ value }) => (
  <td oncreate={rawHtml(value)} onupdate={rawHtml(value)}></td>
);

const ValueRelation = ({ column, row, value }) => (
  <td><a onclick={e => column.onClick({ e, row })}>{value}</a></td>
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
          {state.checkedCount ?
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
            </th> :
            state.columns.map(column => (
              <th class={cc([{ 'mdui-table-col-numeric': column.type === 'number' }])}>{column.title}</th>
            ))
          }
          {!state.checkedCount && <th class="actions">
            {state.orders.length && <button
              class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
              mdui-tooltip={'{content: \'排序选项\', delay: 300}'}
              mdui-menu="{target: '#datatable-sort-menu', covered: false}"
            >
              <i class="mdui-icon material-icons">sort</i>
            </button>}
            {state.orders.length && <ul class="mdui-menu" id="datatable-sort-menu">
              {state.orders.map(_order => (
                <li class="mdui-menu-item">
                  <a href="" onclick={e => actions.changeOrder({ e, order: _order.value, onChange: loadData })}>
                    <i class="mdui-menu-item-icon mdui-icon material-icons">{_order.value === state.order ? 'check' : ''}</i>
                    {_order.name}
                  </a>
                </li>
              ))}
            </ul>}
          </th>}
        </tr>
        </thead>
        {isLoading && <Loading/>}
        {isEmpty && <Empty/>}
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
            {state.columns.map((column) => {
              const value = eval(`row.${column.field}`);

              switch (column.type) {
                case 'number':
                  return <ValueNumber value={value}/>;
                case 'time':
                  return <ValueTime value={value}/>;
                case 'relation':
                  return <ValueRelation column={column} row={row} value={value} />;
                case 'html':
                  return <ValueHtml value={value}/>;
                default:
                  return <td>{value}</td>;
              }
            })}
            <td class="actions">
              {state.buttons.map((button) => {
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
                    return '';
                }
              })}
            </td>
          </tr>
        ))}
        </tbody>
      </table>
      <Pagination onChange={loadData} loading={state.loading}/>
    </div>
  );
};
