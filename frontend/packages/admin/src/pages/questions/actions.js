import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Question } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions';

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

    Question.getList({
      page: datatableState.pagination.page,
      per_page: datatableState.pagination.per_page,
      order: datatableState.order,
    }, (response) => {
      const columns = [
        {
          title: 'ID',
          field: 'question_id',
          type: 'string',
        },
        {
          title: '作者',
          field: 'relationship.user.username',
          type: 'relation',
          relation: 'user',
          relation_id: 'relationship.user.user_id',
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
          type: 'link',
          getLink: question => `${window.G_ROOT}/questions/${question.question_id}`,
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

      response.primaryKey = 'question_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      datatableActions.loadEnd(response);
    });
  },

  /**
   * 编辑指定提问
   */
  editOne: question => (state, actions) => {

  },

  /**
   * 删除指定提问
   */
  deleteOne: question => (state, actions) => {
    mdui.confirm('删除后，你仍可以在回收站中恢复该提问', '确定删除该提问？', () => {
      // todo 添加删除加载动画
      Question.deleteOne(question.question_id, (response) => {
        if (response.code) {
          mdui.snackbar(response.message);
          return;
        }

        actions.loadData();
      });
    }, () => {}, {
      confirmText: '确认',
      cancelText: '取消',
    });
  },

  /**
   * 批量删除提问
   */
  batchDelete: questions => (state, actions) => {
  },
});
