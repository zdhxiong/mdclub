import { h } from 'hyperapp';
import mdui from 'mdui';
import cc from 'classcat';
import timeHelper from '../../helper/time';
import './index.less';

import Datatable from '../../lazyComponents/datatable/view';

export default (global_state, global_actions) => {
  const actions = global_actions.questions;
  const state = global_state.questions;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      key={match.url}
      id="page-questions"
      class="mdui-container-fluid"
    >
      <Datatable loadData={actions.loadData}/>
    </div>
  );
};
