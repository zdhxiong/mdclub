import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import './index.less';

import Loading from '../../components/loading';

export default () => (global_state, global_actions) => {
  const actions = global_actions.lazyComponents.dialogAnswer;
  const state = global_state.lazyComponents.dialogAnswer;

  return (<div>test</div>);
};
