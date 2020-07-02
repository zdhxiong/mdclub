import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getUsers,
  disable as disableUser,
  disableMultiple as disableMultipleUsers,
} from 'mdclub-sdk-js/es/UserApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

const as = {
  /**
   * 初始化
   */
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('用户管理');

    emit('searchbar_state_update', {
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

    emit('datatable_state_update', {
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
      onRowClick: (user) => {
        emit('user_open', user);
        emit('datatable_state_update', {
          lastVisitId: user.user_id,
        });
      },
    });

    $document.on('search-submit', actions.loadData);
    actions.loadData();
  },

  onDestroy: () => {
    $document.off('search-submit');
  },

  /**
   * 加载数据
   */
  loadData: () => {
    const { datatable, pagination, searchBar } = window.app;
    const { page, per_page } = pagination.getState();
    const { order } = datatable.getState();

    const searchData = R.pipe(
      R.filter((n) => n),
      R.mergeDeepLeft({ page, per_page, order }),
    )(searchBar.getState().data);

    datatable.loadStart();

    getUsers(searchData).then(datatable.loadSuccess).catch(datatable.loadFail);
  },

  /**
   * 编辑指定用户
   */
  editOne: (user) => {
    emit('user_edit_open', user);
  },

  /**
   * 禁用指定用户
   */
  disableOne: ({ user_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      disableUser({ user_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认禁用该账号？', confirm, () => {}, options);
  },

  /**
   * 批量禁用用户
   */
  batchDisable: (users) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const user_ids = R.pipe(R.pluck('user_id'), R.join(','))(users);

      disableMultipleUsers({ user_ids })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认禁用这 ${users.length} 个账号`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
