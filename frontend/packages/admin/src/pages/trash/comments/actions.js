import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Comment } from 'mdclub-sdk-js';
import ObjectHelper from '../../../helper/obj';
import actionsAbstract from '../../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {

  },

  /**
   * 销毁前
   */
  destroy: () => {
    $(document).off('search-submit');
  },

  /**
   * 加载数据
   */
  loadData: () => {

  },
});
