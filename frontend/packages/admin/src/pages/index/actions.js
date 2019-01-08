import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange();
    global_actions = props.global_actions;
    global_actions.components.searchBar.setState({ isNeedRender: false });
  },

  /**
   * 销毁前
   */
  destroy: () => {
  },
});
