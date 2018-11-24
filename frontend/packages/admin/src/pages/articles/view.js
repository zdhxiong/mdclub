import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.articles;
  const state = global_state.articles;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-articles"
      class="mdui-container-fluid"
    >

    </div>
  );
};
