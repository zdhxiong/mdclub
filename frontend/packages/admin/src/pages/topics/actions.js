import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import { location } from '@hyperapp/router';
import { Topic } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange('话题管理 - MDClub 控制台');
    global_actions = props.global_actions;

    const { topic, searchBar, datatable } = global_actions.components;

    searchBar.setState({
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

    datatable.setState({
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
          handler: row => `${row.follower_count} 人关注`,
          width: 154,
        },
      ],
      buttons: [
        {
          type: 'target',
          getTargetLink: ({ topic_id }) => `${window.G_ROOT}/topics/${topic_id}`,
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
      onRowClick: topic.open,
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

    Topic
      .getList(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定话题
   */
  editOne: (topic) => {
    global_actions.components.topicEdit.open(topic);
  },

  /**
   * 删除指定话题
   */
  deleteOne: ({ topic_id }) => (state, actions) => {
    const confirm = () => {
      loading.start();

      Topic
        .deleteOne(topic_id)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('删除后，你仍可以在回收站中恢复该话题', '确认删除该话题', confirm, false, options);
  },

  /**
   * 批量删除话题
   */
  batchDelete: topics => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      const topic_ids = R.pipe(
        R.pluck('topic_id'),
        R.join(','),
      )(topics);

      Topic
        .deleteMultiple(topic_ids)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些话题', `确认删除这 ${topics.length} 个话题`, confirm, false, options);
  },
});
