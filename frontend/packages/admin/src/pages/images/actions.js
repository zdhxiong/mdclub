import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Image } from 'mdclub-sdk-js';

let global_actions;

export default {
  setState: value => (value),
  getState: () => state => state,

  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.routeChange();
  },
};
