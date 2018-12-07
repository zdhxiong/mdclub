import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Question } from 'mdclub-sdk-js';
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
          name: 'question_id',
          label: '提问ID',
        },
        {
          name: 'user_id',
          label: '用户ID',
        },
      ],
      data: {
        question_id: '',
        user_id: '',
      },
      isDataEmpty: true,
    });
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;
    const paginationActions = global_actions.lazyComponents.pagination;
    datatableActions.setState({ order: '-create_time' });
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
          field: 'question_id',
          type: 'number',
        },
        {
          title: '作者',
          field: 'relationship.user.username',
          type: 'relation',
          onClick: ({ e, row }) => {
            e.preventDefault();
            global_actions.lazyComponents.dialogUser.open(row.user_id);
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
          getTargetLink: question => `${window.G_ROOT}/questions/${question.question_id}`,
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
    };

    Question.getList(data, success);
  },

  /**
   * 编辑指定提问
   */
  editOne: question => (state, actions) => {

  },

  /**
   * 删除指定提问
   */
  deleteOne: ({ question_id }) => (state, actions) => {
    const confirm = () => {
      $.loadStart();
      Question.deleteOne(question_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复该提问', '确认删除该提问？', confirm, false, options);
  },

  /**
   * 批量删除提问
   */
  batchDelete: questions => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const question_ids = [];
      questions.map((question) => {
        question_ids.push(question.question_id);
      });

      Question.deleteMultiple(question_ids.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些提问', `确认删除这 ${questions.length} 个提问？`, confirm, false, options);
  },
});
