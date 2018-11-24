import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Report } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.routeChange();
  },
});
