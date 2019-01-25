import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Answer } from 'mdclub-sdk-js';
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
      dialogAnswer,
      dialogUser,
    } = global_actions.components;

    const searchBarState = {
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
      isDataEmpty: true,
      isNeedRender: true,
    };

    const columns = [
      {
        title: '作者',
        field: 'relationship.user.username',
        type: 'relation',
        width: 160,
        onClick: ({ e, row }) => {
          e.preventDefault();
          dialogUser.open(row.user_id);
        },
      },
      {
        title: '回答',
        field: 'content_markdown',
        type: 'string',
      },
      {
        title: '创建时间',
        field: 'create_time',
        type: 'time',
        width: 154,
      },
    ];

    const buttons = [
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

    const batchButtons = [
      {
        label: '批量删除',
        icon: 'delete',
        onClick: actions.batchDelete,
      },
    ];

    const orders = [
      {
        name: '创建时间',
        value: '-create_time',
      },
      {
        name: '上次更新时间',
        value: '-update_time',
      },
      {
        name: '投票数',
        value: '-vote_count',
      },
    ];

    const order = '-create_time';
    const primaryKey = 'answer_id';
    const onRowClick = dialogAnswer.open;

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

    Answer.getList(data, datatable.loadEnd);
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
