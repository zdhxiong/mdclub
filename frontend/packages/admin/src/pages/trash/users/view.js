import { h } from 'hyperapp';
import './index.less';

import Datatable from '../../../lazyComponents/datatable/view';

export default (global_state, global_actions) => {
  const actions = global_actions.trashUsers;
  const state = global_state.trashUsers;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-trash-users"
      class="mdui-container-fluid"
    >
      <Datatable loadData={actions.loadData}/>
    </div>
  );
};
