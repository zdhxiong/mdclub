import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { User } from 'mdclub-sdk-js';
import ObjectHelper from '../../helper/obj';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

const searchBarState = {
  fields: [
    {
      name: 'user_id',
      label: '用户ID',
    },
    {
      name: 'username',
      label: '用户名',
    },
    {
      name: 'email',
      label: '邮箱',
    },
  ],
  data: {
    user_id: '',
    username: '',
    email: '',
  },
  isDataEmpty: true,
  isNeedRender: true,
};

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange();
    global_actions = props.global_actions;
    global_actions.lazyComponents.searchBar.setState(searchBarState);

    $(document).on('search-submit', () => {
      actions.loadData();
    });

    actions.loadData();
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
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;

    datatableActions.loadStart();

    const datatableState = datatableActions.getState();
    const paginationState = global_actions.lazyComponents.pagination.getState();
    const searchBarData = global_actions.lazyComponents.searchBar.getState().data;

    const data = $.extend({}, ObjectHelper.filter(searchBarData), {
      page: paginationState.page,
      per_page: paginationState.per_page,
      order: datatableState.order,
    });

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

      const orders = [
        {
          name: '注册时间',
          value: '-create_time',
        },
        {
          name: '关注者数量',
          value: '-follower_count',
        },
      ];

      response.data.map((item, index) => {
        response.data[index].avatar_username = `<img src="${item.avatar.s}" class="mdui-float-left mdui-m-r-2"/>${item.username}`;
      });

      response.primaryKey = 'user_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      response.orders = orders;
      response.order = '-create_time';
      response.onRowClick = global_actions.lazyComponents.dialogUser.open;
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
