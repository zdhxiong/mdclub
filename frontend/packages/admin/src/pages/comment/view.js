import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.comment;
  const state = global_state.comment;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-comment"
      class="mdui-container-fluid"
    >

    </div>
  );
};
