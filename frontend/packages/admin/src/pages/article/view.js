import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.article;
  const state = global_state.article;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-article"
      class="mdui-container-fluid"
    >

    </div>
  );
};
