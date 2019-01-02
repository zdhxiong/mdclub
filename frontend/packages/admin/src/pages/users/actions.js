import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { User } from 'mdclub-sdk-js';
import ObjectHelper from '../../helper/obj';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange();
    global_actions = props.global_actions;

    const {
      searchBar,
      datatable,
      dialogUser,
    } = global_actions.lazyComponents;

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

    const columns = [
      {
        title: 'ID',
        field: 'user_id',
        type: 'number',
        width: 100,
      },
      {
        title: '用户名',
        field: 'avatar_username',
        type: 'html',
        width: 208,
      },
      {
        title: '一句话介绍',
        field: 'headline',
        type: 'string',
      },
      {
        title: '注册时间',
        field: 'create_time',
        type: 'time',
        width: 200,
      },
    ];

    const buttons = [
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

    const batchButtons = [
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

    const order = '-create_time';
    const primaryKey = 'user_id';
    const onRowClick = dialogUser.open;

    searchBar.setState(searchBarState);
    datatable.setState({
      columns,
      buttons,
      batchButtons,
      orders,
      order,
      primaryKey,
      onRowClick,
    });

    $(document).on('search-submit', actions.loadData);
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
  loadData: () => {
    const { datatable, pagination, searchBar } = global_actions.lazyComponents;

    datatable.loadStart();

    const data = $.extend({}, ObjectHelper.filter(searchBar.getState().data), {
      page: pagination.getState().page,
      per_page: pagination.getState().per_page,
      order: datatable.getState().order,
    });

    User.getList(data, (response) => {
      response.data.map((item, index) => {
        response.data[index].avatar_username = `<img src="${item.avatar.s}" class="mdui-float-left mdui-m-r-2"/>${item.username}`;
      });

      datatable.loadEnd(response);
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
