import { h } from 'hyperapp';
import cc from 'classcat';
import timeHelper from '../../helper/time';
import rawHtml from '../../helper/rawHtml';
import './index.less';

import Loading from '../../components/loading';
import Empty from '../../components/empty';
import Pagination from '../../lazyComponents/pagination/view';

const CheckAll = ({ isChecked, onChange }) => (
  <label class="mdui-checkbox">
    <input type="checkbox" checked={isChecked} onchange={onChange}/>
    <i class="mdui-checkbox-icon"></i>
  </label>
);

const CheckOne = ({ isChecked, onChange }) => (
  <label class="mdui-checkbox">
    <input type="checkbox" checked={isChecked} onchange={onChange}/>
    <i class="mdui-checkbox-icon"></i>
  </label>
);

const Time = ({ timestamp }) => (
  <span title={timeHelper.format(timestamp)}>{timeHelper.friendly(timestamp)}</span>
);

const ActionBtn = ({ label, icon, onClick }) => (
  <button
    class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
    mdui-tooltip={`{content: '${label}', delay: 300}`}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">{icon}</i>
  </button>
);

const ActionTarget = ({ target }) => (
  <a
    href={target}
    target="_blank"
    class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
    mdui-tooltip="{content: '新窗口打开', delay: 300}"
  >
    <i class="mdui-icon material-icons">open_in_new</i>
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
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
    >
      <table class="mdui-table mdui-table-hoverable">
        <thead>
        <tr class={cc([
          {
            checked: state.checkedCount,
          },
        ])}>
          <th class="mdui-table-cell-checkbox">
            <CheckAll
              isChecked={state.isCheckedAll}
              onChange={e => actions.checkAll(e)}
            />
          </th>
          {state.checkedCount ?
            <th colspan={state.columns.length}>
              <span>
              {state.batchActions.map(action => (
                <ActionBtn
                  label={action.label}
                  icon={action.icon}
                  onClick={() => {
                    const items = [];
                    state.data.map((item) => {
                      if (state.isCheckedRows[item[state.primaryKey]]) {
                        items.push(item);
                      }
                    });
                    action.onClick(items);
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
          {state.checkedCount ? '' : <th></th>}
        </tr>
        </thead>
        {isLoading ? <Loading/> : ''}
        {isEmpty ? <Empty/> : ''}
        <tbody class={cc([
          {
            'is-loading': isLoading,
            'is-empty': isEmpty,
          },
        ])}>
        {state.data.map(row => (
          <tr
            key={row[state.primaryKey]}
            class={cc([
              {
                'mdui-table-row-selected': state.isCheckedRows[row[state.primaryKey]],
              },
            ])}
            onclick={(e) => {
              if (typeof state.onRowClick !== 'function') {
                return;
              }

              if (e.target.nodeName !== 'TD') {
                return;
              }

              state.onRowClick(row[state.primaryKey]);
            }}
          >
            <td class="mdui-table-cell-checkbox">
              <CheckOne
                isChecked={state.isCheckedRows[row[state.primaryKey]]}
                onChange={() => actions.checkOne(row[state.primaryKey])}
              />
            </td>
            {state.columns.map((column) => {
              const value = eval(`row.${column.field}`);

              switch (column.type) {
                case 'number':
                  return <td class={cc([{ 'mdui-table-col-numeric': column.type === 'number' }])}>{value}</td>;
                case 'time':
                  return <td><Time timestamp={value}/></td>;
                case 'relation':
                  return <td><a href="" onclick={e => column.onClick({ e, row })}>{value}</a></td>;
                case 'html':
                  return <td oncreate={rawHtml(value)} onupdate={rawHtml(value)}></td>;
                default:
                  return <td>{value}</td>;
              }
            })}
            <td class="actions">
              {state.actions.map((action) => {
                switch (action.type) {
                  case 'target':
                    return <ActionTarget target={action.getTargetLink(row)}/>;
                  case 'btn':
                    return <ActionBtn
                      label={action.label}
                      icon={action.icon}
                      onClick={() => action.onClick(row)}
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
