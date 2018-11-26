import { h } from 'hyperapp';
import './index.less';

import Datatable from '../../lazyComponents/datatable/view';

export default (global_state, global_actions) => {
  const actions = global_actions.users;
  const state = global_state.users;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-users"
      class="mdui-container-fluid"
    >
      <Datatable loadData={actions.loadData}/>
    </div>
  );
};
