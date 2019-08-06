import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import { location } from '@hyperapp/router';
import { Comment } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange('评论管理 - MDClub 控制台');
    global_actions = props.global_actions;

    const { user, comment, searchBar, datatable } = global_actions.components;

    searchBar.setState({
      fields: [
        { name: 'comment_id', label: '评论ID' },
        { name: 'user_id', label: '用户ID' },
        {
          name: 'commentable_type',
          label: '类型',
          enum: [
            { name: '全部', value: '' },
            { name: '文章', value: 'article' },
            { name: '提问', value: 'question' },
            { name: '回答', value: 'answer' },
          ],
        },
        { name: 'commentable_id', label: '评论目标ID' },
      ],
      data: {
        comment_id: '',
        commentable_id: '',
        commentable_type: '',
        user_id: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    });

    datatable.setState({
      columns: [
        {
          title: '作者',
          field: 'relationship.user.username',
          type: 'relation',
          width: 160,
          onClick: ({ e, row }) => {
            e.preventDefault();
            user.open(row.user_id);
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
          width: 154,
        },
      ],
      buttons: [
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
        { name: '创建时间', value: '-create_time' },
        { name: '投票数', value: '-vote_count' },
      ],
      order: '-create_time',
      primaryKey: 'comment_id',
      onRowClick: comment.open,
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

    Comment
      .getList(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
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
      loading.start();

      Comment
        .deleteOne(comment_id)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('删除后，你仍可以在回收站中恢复该评论', '确认删除该评论', confirm, false, options);
  },

  /**
   * 批量删除评论
   */
  batchDelete: comments => (state, actions) => {
    const confirm = () => {
      loading.start();

      const comment_ids = R.pipe(
        R.pluck('comment_id'),
        R.join(','),
      )(comments);

      Comment
        .deleteMultiple(comment_ids)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些评论', `确认删除这 ${comments.length} 条评论`, confirm, false, options);
  },
});
