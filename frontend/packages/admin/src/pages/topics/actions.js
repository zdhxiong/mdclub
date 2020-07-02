import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getTopics,
  del as deleteTopic,
  deleteMultiple as deleteMultipleTopics,
} from 'mdclub-sdk-js/es/TopicApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('话题管理');

    emit('searchbar_state_update', {
      fields: [
        { name: 'topic_id', label: '话题ID' },
        { name: 'name', label: '话题名称' },
      ],
      data: {
        topic_id: '',
        name: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    });

    emit('datatable_state_update', {
      columns: [
        {
          title: '名称',
          field: 'name',
          type: 'string',
          width: 208,
        },
        {
          title: '描述',
          field: 'description',
          type: 'string',
        },
        {
          title: '关注者数量',
          field: 'follower_count',
          type: 'handler',
          handler: (row) => `${row.follower_count} 人关注`,
          width: 154,
        },
      ],
      buttons: [
        {
          type: 'target',
          getTargetLink: ({ topic_id }) => {
            return `${window.G_ROOT}/topics/${topic_id}`;
          },
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
          label: '删除',
          icon: 'delete',
        },
      ],
      batchButtons: [
        {
          label: '批量删除',
          icon: 'delete',
          onClick: actions.batchDelete,
        },
      ],
      orders: [
        { name: '话题ID', value: 'topic_id' },
        { name: '关注者数量', value: '-follower_count' },
      ],
      order: 'topic_id',
      primaryKey: 'topic_id',
      onRowClick: (topic) => {
        emit('topic_open', topic);
        emit('datatable_state_update', {
          lastVisitId: topic.topic_id,
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

    getTopics(searchData).then(datatable.loadSuccess).catch(datatable.loadFail);
  },

  /**
   * 编辑指定话题
   */
  editOne: (topic) => {
    emit('topic_edit_open', topic);
  },

  /**
   * 删除指定话题
   */
  deleteOne: ({ topic_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      deleteTopic({ topic_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认删除该话题？', confirm, () => {}, options);
  },

  /**
   * 批量删除话题
   */
  batchDelete: (topics) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const topic_ids = R.pipe(R.pluck('topic_id'), R.join(','))(topics);

      deleteMultipleTopics({ topic_ids })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认删除这 ${topics.length} 个话题？`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
