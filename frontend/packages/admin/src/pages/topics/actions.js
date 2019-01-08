import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Topic } from 'mdclub-sdk-js';
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
      dialogTopic,
    } = global_actions.components;

    const searchBarState = {
      fields: [
        {
          name: 'topic_id',
          label: '话题ID',
        },
        {
          name: 'name',
          label: '话题名称',
        },
      ],
      data: {
        topic_id: '',
        name: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    };

    const columns = [
      {
        title: 'ID',
        field: 'topic_id',
        type: 'number',
        width: 100,
      },
      {
        title: '名称',
        field: 'name',
        type: 'string',
      },
      {
        title: '关注者数量',
        field: 'follower_count',
        type: 'number',
        width: 200,
      },
    ];

    const buttons = [
      {
        type: 'target',
        getTargetLink: topic => `${window.G_ROOT}/topics/${topic.topic_id}`,
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
    ];

    const batchButtons = [
      {
        label: '批量删除',
        icon: 'delete',
        onClick: actions.batchDelete,
      },
    ];

    const orders = [
      {
        name: '话题ID',
        value: 'topic_id',
      },
      {
        name: '关注者数量',
        value: '-follower_count',
      },
    ];

    const order = 'topic_id';
    const primaryKey = 'topic_id';
    const onRowClick = dialogTopic.open;

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
    const { datatable, pagination, searchBar } = global_actions.components;

    datatable.loadStart();

    const data = $.extend({}, ObjectHelper.filter(searchBar.getState().data), {
      page: pagination.getState().page,
      per_page: pagination.getState().per_page,
      order: datatable.getState().order,
    });

    Topic.getList(data, datatable.loadEnd);
  },

  /**
   * 编辑指定话题
   */
  editOne: topic => (state, actions) => {

  },

  /**
   * 删除指定话题
   */
  deleteOne: ({ topic_id }) => (state, actions) => {
    const confirm = () => {
      $.loadStart();
      Topic.deleteOne(topic_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复该话题', '确认删除该话题', confirm, false, options);
  },

  /**
   * 批量删除话题
   */
  batchDelete: topics => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const topic_ids = [];
      topics.map((topic) => {
        topic_ids.push(topic.topic_id);
      });

      Topic.deleteMultiple(topic_ids.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些话题', `确认删除这 ${topics.length} 个话题`, confirm, false, options);
  },
});
