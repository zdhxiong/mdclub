import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Comment } from 'mdclub-sdk-js';
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
      dialogComment,
      dialogUser,
    } = global_actions.components;

    const searchBarState = {
      fields: [
        {
          name: 'comment_id',
          label: '评论ID',
        },
        {
          name: 'user_id',
          label: '用户ID',
        },
        {
          name: 'commentable_type',
          label: '类型',
          enum: [
            {
              name: '全部',
              value: '',
            },
            {
              name: '文章',
              value: 'article',
            },
            {
              name: '提问',
              value: 'question',
            },
            {
              name: '回答',
              value: 'answer',
            },
          ],
        },
        {
          name: 'commentable_id',
          label: '评论目标ID',
        },
      ],
      data: {
        comment_id: '',
        commentable_id: '',
        commentable_type: '',
        user_id: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    };

    const columns = [
      {
        title: 'ID',
        field: 'comment_id',
        type: 'number',
        width: 100,
      },
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
        title: '内容',
        field: 'content',
        type: 'string',
      },
      {
        title: '创建时间',
        field: 'create_time',
        type: 'time',
        width: 200,
      },
    ];

    const buttons = [
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
        name: '投票数',
        value: '-vote_count',
      },
    ];

    const order = '-create_time';
    const primaryKey = 'comment_id';
    const onRowClick = dialogComment.open;

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

    Comment.getList(data, datatable.loadEnd);
  },

  /**
   * 编辑指定评论
   */
  editOne: comment => (state, actions) => {

  },

  /**
   * 删除指定评论
   */
  deleteOne: ({ comment_id }) => (state, actions) => {
    const confirm = () => {
      $.loadStart();
      Comment.deleteOne(comment_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复该评论', '确认删除该评论', confirm, false, options);
  },

  /**
   * 批量删除评论
   */
  batchDelete: comments => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const comment_ids = [];
      comments.map((comment) => {
        comment_ids.push(comment.comment_id);
      });

      Comment.deleteMultiple(comment_ids.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些评论', `确认删除这 ${comments.length} 条评论`, confirm, false, options);
  },
});
