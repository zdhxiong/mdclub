import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import { location } from '@hyperapp/router';
import { User } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange('用户管理 - MDClub 控制台');
    global_actions = props.global_actions;

    const { user, searchBar, datatable } = global_actions.components;

    searchBar.setState({
      fields: [
        { name: 'user_id', label: '用户ID' },
        { name: 'username', label: '用户名' },
        { name: 'email', label: '邮箱' },
      ],
      data: {
        user_id: '',
        username: '',
        email: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    });

    datatable.setState({
      columns: [
        {
          title: '用户名',
          field: 'username',
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
          width: 154,
        },
      ],
      buttons: [
        {
          type: 'target',
          getTargetLink: ({ user_id }) => `${window.G_ROOT}/users/${user_id}`,
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
      ],
      batchButtons: [
        {
          label: '批量禁用',
          icon: 'lock',
          onClick: actions.batchDisable,
        },
      ],
      orders: [
        { name: '注册时间', value: '-create_time' },
        { name: '关注者数量', value: '-follower_count' },
      ],
      order: '-create_time',
      primaryKey: 'user_id',
      onRowClick: user.open,
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
    const { datatable, pagination, searchBar } = global_actions.components;
    const { page, per_page } = pagination.getState();
    const { order } = datatable.getState();

    const searchData = R.pipe(
      R.filter(n => n),
      R.mergeDeepLeft({ page, per_page, order }),
    )(searchBar.getState().data);

    datatable.loadStart();

    User
      .getList(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定用户
   */
  editOne: user => (state, actions) => {

  },

  /**
   * 禁用指定用户
   */
  disableOne: ({ user_id }) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      User
        .disableOne(user_id)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm('确认禁用该账号？', confirm, false, options);
  },

  /**
   * 批量禁用用户
   */
  batchDisable: users => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      const user_ids = R.pipe(
        R.pluck('user_id'),
        R.join(','),
      )(users);

      User
        .disableMultiple(user_ids)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(`确认禁用这 ${users.length} 个账号`, confirm, false, options);
  },
});
