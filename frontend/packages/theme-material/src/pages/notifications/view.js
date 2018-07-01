import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.notifications;
  const state = global_state.notifications;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-notifications"
      class="mdui-container"
    >notifications</div>
  );
};
