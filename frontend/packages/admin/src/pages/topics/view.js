import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.topics;
  const state = global_state.topics;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-topics"
      class="mdui-container-fluid"
    >
      <div class="mdui-table-fluid">
        <table class="mdui-table mdui-table-selectable mdui-table-hoverable">
          <thead>
          <tr>
            <th>ID</th>
            <th>名称</th>
            <th>Logo</th>
            <th>描述</th>
            <th>文章</th>
            <th>问题</th>
            <th>关注者</th>
            <th>操作</th>
          </tr>
          </thead>
          <tbody>
          {state.data.map(topic => (
            <tr>
              <td>{topic.topic_id}</td>
              <td>{topic.name}</td>
              <td>{topic.cover.s}</td>
              <td>{topic.description}</td>
              <td>{topic.article_count}</td>
              <td>{topic.question_count}</td>
              <td>{topic.follower_count}</td>
              <td></td>
            </tr>
          ))}
          </tbody>
          <tfoot>
          <tr>
            <td colspan="8"></td>
          </tr>
          </tfoot>
        </table>
      </div>
    </div>
  );
};
