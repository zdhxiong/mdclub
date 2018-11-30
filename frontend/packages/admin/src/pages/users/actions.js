import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { User } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/pageActions';

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

    const data = {
      page: datatableState.pagination.page,
      per_page: datatableState.pagination.per_page,
      order: datatableState.order,
    };

    const success = (response) => {
      const columns = [
        {
          title: 'ID',
          field: 'user_id',
          type: 'number',
        },
        {
          title: '用户名',
          field: 'avatar_username',
          type: 'html',
        },
      ];

      const _actions = [
        {
          type: 'target',
          getTargetLink: user => `${window.G_ROOT}/users/${user.user_id}`,
        },
        {
          type: 'btn',
          onClick: actions.editOne,
          label: '编辑',
          icon: 'edit',
        },
        {
          type: 'btn',
          onClick: actions.disableOne,
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

      response.data.map((item, index) => {
        response.data[index].avatar_username = `<img src="${item.avatar.s}" class="mdui-float-left mdui-m-r-2"/>${item.username}`;
      });

      response.primaryKey = 'user_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      response.onRowClick = global_actions.lazyComponents.userDialog.open;
      datatableActions.loadEnd(response);
    };

    User.getList(data, success);
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
    const confirm = () => {
      $.loadStart();
      User.disableOne(user.user_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('确认禁用该账号？', confirm, false, options);
  },

  /**
   * 批量禁用用户
   */
  batchDisable: users => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const user_ids = [];
      users.map((user) => {
        user_ids.push(user.user_id);
      });

      User.disableMultiple(user_ids.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm(`确认禁用这 ${users.length} 个账号`, confirm, false, options);
  },
});
