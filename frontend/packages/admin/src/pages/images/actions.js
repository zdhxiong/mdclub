import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Image } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    actions.routeChange();
    actions.loadData();
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;
    const paginationActions = global_actions.lazyComponents.pagination;
    datatableActions.loadStart();

    const paginationState = paginationActions.getState();

    const data = {
      page: paginationState.page,
      per_page: paginationState.per_page,
    };

    Image.getList(data, () => {

    });
  },
});
