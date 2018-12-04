import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.trash_answers;
  const state = global_state.trash_answers;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-trash-answers"
      class="mdui-container-fluid"
    >

    </div>
  );
};
