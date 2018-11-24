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
      key={match.url}
      id="page-questions"
      class="mdui-container-fluid"
    >
      <div class="mdui-table-fluid">
        <table
          class="mdui-table mdui-table-selectable mdui-table-hoverable"
          oncreate={element => mdui.updateTables(element)}
          onupdate={element => mdui.updateTables(element)}
        >
          <thead>
          <tr>
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
            }
          ])}>
          {state.data.map(question => (
            <tr key={question.question_id}>
              <td>{question.question_id}</td>
              <td>{question.relationship.user.username}</td>
              <td>{question.title}</td>
              <td>
                <span title={timeHelper.format(question.create_time)}>
                  {timeHelper.friendly(question.create_time)}
                </span>
              </td>
              <td></td>
            </tr>
          ))}
          </tbody>
        </table>
        <Pagination onPaginationChange={actions.loadData}/>
      </div>
    </div>
  );
};
