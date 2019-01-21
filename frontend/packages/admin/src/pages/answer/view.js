import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.answer;
  const state = global_state.answer;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-answer"
      class="mdui-container-fluid"
    >

    </div>
  );
};
