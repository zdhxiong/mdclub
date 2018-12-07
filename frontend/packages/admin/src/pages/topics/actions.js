import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Topic } from 'mdclub-sdk-js';
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

    global_actions.lazyComponents.searchBar.setState({
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
    });
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;
    const paginationActions = global_actions.lazyComponents.pagination;
    datatableActions.loadStart();

    const datatableState = datatableActions.getState();
    const paginationState = paginationActions.getState();

    const data = {
      page: paginationState.page,
      per_page: paginationState.per_page,
      order: datatableState.order,
    };

    const success = (response) => {
      const columns = [
        {
          title: 'ID',
          field: 'topic_id',
          type: 'number',
        },
        {
          title: '名称',
          field: 'name',
          type: 'string',
        },
      ];

      const _actions = [
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

      const batchActions = [
        {
          label: '批量删除',
          icon: 'delete',
          onClick: actions.batchDelete,
        },
      ];

      response.primaryKey = 'topic_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      response.onRowClick = global_actions.lazyComponents.dialogTopic.open;
      datatableActions.loadEnd(response);
    };

    Topic.getList(data, success);
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
