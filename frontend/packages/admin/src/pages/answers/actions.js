import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Answer } from 'mdclub-sdk-js';
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
          name: 'answer_id',
          label: '回答ID',
        },
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
        answer_id: '',
        question_id: '',
        user_id: '',
      },
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
          field: 'answer_id',
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
          title: '回答',
          field: 'content_markdown',
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
          getTargetLink: answer => `${window.G_ROOT}/questions/${answer.relationship.question.question_id}`,
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

      response.primaryKey = 'answer_id';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      datatableActions.loadEnd(response);
    };

    Answer.getList(data, success);
  },

  /**
   * 编辑指定回答
   */
  editOne: answer => (state, actions) => {

  },

  /**
   * 删除指定回答
   */
  deleteOne: ({ answer_id }) => (state, actions) => {
    const confirm = () => {
      $.loadStart();
      Answer.deleteOne(answer_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复该回答', '确认删除该回答', confirm, false, options);
  },

  /**
   * 批量删除回答
   */
  batchDelete: answers => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const answer_ids = [];
      answers.map((answer) => {
        answer_ids.push(answer.answer_id);
      });

      Answer.deleteMultiple(answer_ids.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些回答', `确认删除这 ${answers.length} 个回答`, confirm, false, options);
  },
});
