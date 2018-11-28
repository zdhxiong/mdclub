import { h } from 'hyperapp';
import './index.less';

import Datatable from '../../lazyComponents/datatable/view';

export default (global_state, global_actions) => {
  const actions = global_actions.reports;
  const state = global_state.reports;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-reports"
      class="mdui-container-fluid"
    >
      <Datatable loadData={actions.loadData}/>
    </div>
  );
};
