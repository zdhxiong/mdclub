import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.comments;
  const state = global_state.comments;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-comments"
    >

    </div>
  );
};
