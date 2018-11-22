import { h } from 'hyperapp';
import mdui from 'mdui';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../components/loading';
import Empty from '../../components/empty';
import Pagination from '../../components/pagination';

export default (global_state, global_actions) => {
  const actions = global_actions.questions;
  const state = global_state.questions;
  const isEmpty = !state.loading && !state.data.length && state.pagination;
  const isLoading = state.loading;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-questions"
      class="mdui-container-fluid"
    >
      {isLoading ? <Loading/> : ''}
      {isEmpty ? <Empty/> :
        <div class="mdui-table-fluid">
          <table
            class="mdui-table mdui-table-selectable mdui-table-hoverable"
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
            <tbody>
            {state.data.map(question => (
              <tr>
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
          <Pagination
            onPrevPageClick={actions.onPrevPageClick}
            onNextPageClick={actions.onNextPageClick}
            onPerPageChange={actions.onPerPageChange}
            onPageChange={actions.onPageChange}
            pagination={state.pagination}
          />
        </div>
      }
    </div>
  );
};
