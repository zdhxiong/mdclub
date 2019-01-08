import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import './index.less';

import Loading from '../../elements/loading';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.dialogComment;
  const state = global_state.components.dialogComment;

  return (<div>test</div>);
};
