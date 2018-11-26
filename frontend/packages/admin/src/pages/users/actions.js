import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { User } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions';

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
    const datatableState = datatableActions.getState();

    datatableActions.loadStart();

    User.getList({
      page: datatableState.pagination.page,
      per_page: datatableState.pagination.per_page,
      order: datatableState.order,
    }, (response) => {
      const columns = [
        {
          title: 'ID',
          field: 'user_id',
          type: 'string',
        },
        {
          title: '用户名',
          field: 'username',
          type: 'string',
        },
      ];

      const _actions = [
        {
          type: 'link',
          getLink: user => `${window.G_ROOT}/users/${user.user_id}`,
        },
        {
          type: 'btn',
          onClick: actions.editOne,
          label: '编辑',
          icon: 'edit',
        },
        {
          type: 'btn',
          onClick: actions.deleteOne,
          label: '禁用',
          icon: 'lock',
        },
      ];

      const batchActions = [
        {
          label: '批量禁用',
          icon: 'lock',
          onClick: actions.batchDisable,
        },
      ];

      response.primaryKey = 'user_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      datatableActions.loadEnd(response);
    });
  },

  /**
   * 编辑指定用户
   */
  editOne: user => (state, actions) => {

  },

  /**
   * 禁用指定用户
   */
  disableOne: user => (state, actions) => {

  },

  /**
   * 批量禁用用户
   */
  batchDisable: user => (state, actions) => {

  },
});
