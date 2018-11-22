import { h } from 'hyperapp';
import timeHelper from '../../helper/time';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.questions;
  const state = global_state.questions;

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
          onupdate={(element) => actions.update({ element })}
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
          {state.data.map(question => {
            return () => (
              <tr>
                <td>{question.question_id}</td>
                <td>{question.relationship.user.username}</td>
                <td>{question.title}</td>
                <td>{timeHelper.format(question.create_time)}</td>
                <td></td>
              </tr>
            );
          })}
          </tbody>
        </table>

        <div class="mc-pagination mdui-toolbar">
          <div class="mdui-toolbar-spacer"></div>
          <span class="per-page mdui-typo-caption">每页条数：</span>
          <select mdui-select>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <div class="range mdui-typo-caption">1-10 of 231</div>
          <button class="mdui-btn mdui-btn-icon mdui-ripple"><i class="mdui-icon material-icons">chevron_left</i></button>
          <button class="mdui-btn mdui-btn-icon mdui-ripple"><i class="mdui-icon material-icons">chevron_right</i></button>
        </div>

      </div>
    </div>
  );
};
