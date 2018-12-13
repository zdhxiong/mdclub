import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.index;
  const state = global_state.index;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key="index"
      id="page-index"
      class="mdui-container-fluid"
    >index</div>
  );
};
