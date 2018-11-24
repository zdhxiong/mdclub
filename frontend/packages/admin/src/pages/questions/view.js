import { h } from 'hyperapp';
import mdui from 'mdui';
import cc from 'classcat';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../components/loading';
import Empty from '../../components/empty';
import Pagination from '../../lazyComponents/pagination/view';

export default (global_state, global_actions) => {
  const actions = global_actions.questions;
  const state = global_state.questions;
  const paginationState = global_state.lazyComponents.pagination;

  const isEmpty = !paginationState.loading && !state.data.length;
  const isLoading = paginationState.loading;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={() => actions.destroyDataList({ global_actions })}
      key={match.url}
      id="page-questions"
      class="mdui-container-fluid"
    >
      <div class="mdui-table-fluid">
        <table class="mdui-table mdui-table-hoverable">
          <thead>
          <tr>
            <th class="mdui-table-cell-checkbox">
              <label class="mdui-checkbox">
                <input type="checkbox"/>
                <i class="mdui-checkbox-icon"></i>
              </label>
            </th>
            <th width="60">ID</th>
            <th width="120">作者</th>
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
          {state.data.map(question => (
            <tr key={question.question_id}>
              <td class="mdui-table-cell-checkbox">
                <label class="mdui-checkbox">
                  <input type="checkbox"/>
                  <i class="mdui-checkbox-icon"></i>
                </label>
              </td>
              <td>{question.question_id}</td>
              <td><a href="">{question.relationship.user.username}</a></td>
              <td>{question.title}</td>
              <td>
                <span title={timeHelper.format(question.create_time)}>
                  {timeHelper.friendly(question.create_time)}
                </span>
              </td>
              <td class="actions">
                <a
                  href={`${window.G_ROOT}/questions/${question.question_id}`}
                  target="_blank"
                  class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
                  mdui-tooltip="{content: '新窗口打开', delay: 300}"
                >
                  <i class="mdui-icon material-icons">open_in_new</i>
                </a>
                <button
                  class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
                  mdui-tooltip="{content: '编辑', delay: 300}"
                >
                  <i class="mdui-icon material-icons">edit</i>
                </button>
                <button
                  class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-text-color-theme-icon"
                  mdui-tooltip="{content: '删除', delay: 300}"
                  onclick={() => actions.deleteOne(question.question_id)}
                >
                  <i class="mdui-icon material-icons">delete</i>
                </button>
              </td>
            </tr>
          ))}
          </tbody>
        </table>
        <Pagination onPaginationChange={actions.loadData}/>
      </div>
    </div>
  );
};
