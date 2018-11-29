import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Article } from 'mdclub-sdk-js';
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

    Article.getList({
      page: datatableState.pagination.page,
      per_page: datatableState.pagination.per_page,
      order: datatableState.order,
    }, (response) => {
      const columns = [
        {
          title: 'ID',
          field: 'article_id',
          type: 'number',
        },
        {
          title: '作者',
          field: 'relationship.user.username',
          type: 'relation',
          onClick: ({ e, row }) => {
            e.preventDefault();
            global_actions.lazyComponents.userDialog.open(row.user_id);
          },
        },
        {
          title: '标题',
          field: 'title',
          type: 'string',
        },
        {
          title: '发表时间',
          field: 'create_time',
          type: 'time',
        },
      ];

      const _actions = [
        {
          type: 'target',
          getTargetLink: article => `${window.G_ROOT}/articles/${article.article_id}`,
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

      response.primaryKey = 'article_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      datatableActions.loadEnd(response);
    });
  },

  /**
   * 编辑指定文章
   */
  editOne: article => (state, actions) => {

  },

  /**
   * 删除指定文章
   */
  deleteOne: article => (state, actions) => {

  },

  /**
   * 批量删除文章
   */
  batchDelete: articles => (state, actions) => {

  },
});
