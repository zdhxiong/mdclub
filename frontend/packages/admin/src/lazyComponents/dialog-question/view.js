import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import './index.less';

import Loading from '../../components/loading';

export default () => (global_state, global_actions) => {
  const actions = global_actions.lazyComponents.dialogQuestion;
  const state = global_state.lazyComponents.dialogQuestion;

  return (<div>test</div>);
};
