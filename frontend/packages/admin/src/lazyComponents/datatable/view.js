import { h } from 'hyperapp';
import mdui, { JQ as $ } from 'mdui';
import cc from 'classcat';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../components/loading';
import Empty from '../../components/empty';

const Spacer = () => (
  <div class="mdui-toolbar-spacer"></div>
);

const PerPage = ({ state, onChange }) => (
  <div class="per-page">
    <span class="label mdui-typo-caption">每页行数：</span>
    <select class="mdui-select" onchange={onChange} disabled={state.loading}>
      <option value="10" selected={state.pagination.per_page === 10}>10</option>
      <option value="25" selected={state.pagination.per_page === 25}>25</option>
      <option value="50" selected={state.pagination.per_page === 50}>50</option>
      <option value="100" selected={state.pagination.per_page === 100}>100</option>
    </select>
  </div>
);

const Page = ({ state, onSubmit }) => (
  <div class="page mdui-typo-caption">
    第
    <form class="form" onsubmit={onSubmit}>
      <input
        class="input mdui-textfield-input"
        type="text"
        name="page"
        autoComplete="off"
        disabled={state.loading}
        value={state.pagination.page}
      />
    </form>
    页，共 {state.pagination.pages} 页
  </div>
);

const PrevPage = ({ state, onClick }) => (
  <button
    class="prev mdui-btn mdui-btn-icon"
    title="上一页"
    disabled={!state.pagination.previous || state.loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_left</i>
  </button>
);

const NextPage = ({ state, onClick }) => (
  <button
    class="next mdui-btn mdui-btn-icon"
    title="下一页"
    disabled={!state.pagination.next || state.loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_right</i>
  </button>
);

const Checkbox = () => (
  <label class="mdui-checkbox">
    <input type="checkbox"/>
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

const ActionTarget = ({ link }) => (
  <a
    href={link}
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
      oncreate={element => actions.init({ element })}
      ondestroy={actions.destroy}
    >
      <table class="mdui-table mdui-table-hoverable">
        <thead>
        <tr>
          <th class="mdui-table-cell-checkbox">
            <Checkbox/>
          </th>
          <th>ID</th>
          <th>作者</th>
          <th>标题</th>
          <th>发表时间</th>
          <th></th>
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
        {state.data.map(item => (
          <tr key={item.question_id}>
            <td class="mdui-table-cell-checkbox">
              <Checkbox/>
            </td>
            <td>{item.question_id}</td>
            <td><a href="">{item.relationship.user.username}</a></td>
            <td>{item.title}</td>
            <td>
              <Time timestamp={item.create_time}/>
            </td>
            <td class="actions">
              <ActionTarget
                link={`${window.G_ROOT}/questions/${item.question_id}`}
              />
              <ActionBtn
                label="编辑"
                icon="edit"
              />
              <ActionBtn
                label="删除"
                icon="delete"
              />
            </td>
          </tr>
        ))}
        </tbody>
      </table>
      <div class="pagination mdui-toolbar">
        <Spacer/>
        <PerPage
          state={state}
          onChange={e => actions.onPerPageChange({e, loadData})}
        />
        <Page
          state={state}
          onSubmit={e => actions.onPageChange({e, loadData})}
        />
        <PrevPage
          state={state}
          onClick={() => actions.toPrevPage(loadData)}
        />
        <NextPage
          state={state}
          onClick={() => actions.toNextPage(loadData)}
        />
      </div>
    </div>
  );
};
