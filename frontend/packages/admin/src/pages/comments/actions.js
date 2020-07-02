import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getComments,
  del as deleteComment,
  deleteMultiple as deleteMultipleComments,
} from 'mdclub-sdk-js/es/CommentApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('评论管理');

    emit('searchbar_state_update', {
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

    emit('datatable_state_update', {
      columns: [
        {
          title: '作者',
          field: 'relationships.user.username',
          type: 'relation',
          width: 160,
          onClick: ({ e, row }) => {
            e.preventDefault();
            emit('user_open', row.user_id);
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
      onRowClick: (comment) => {
        emit('comment_open', comment);
        emit('datatable_state_update', {
          lastVisitId: comment.comment_id,
        });
      },
    });

    $document.on('search-submit', actions.loadData);
    actions.loadData();
  },

  onDestroy: () => {
    $document.off('search-submit');
  },

  /**
   * 加载数据
   */
  loadData: () => {
    const { datatable, pagination, searchBar } = window.app;
    const { page, per_page } = pagination.getState();
    const { order } = datatable.getState();

    const searchData = R.pipe(
      R.filter((n) => n),
      R.mergeDeepLeft({ page, per_page, order }),
    )(searchBar.getState().data);

    searchData.include = ['user'];

    datatable.loadStart();

    getComments(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定评论
   */
  editOne: (comment) => {
    emit('comment_edit_open', comment);
  },

  /**
   * 删除指定评论
   */
  deleteOne: ({ comment_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      deleteComment({ comment_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认删除该评论？', confirm, () => {}, options);
  },

  /**
   * 批量删除评论
   */
  batchDelete: (comments) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const comment_ids = R.pipe(R.pluck('comment_id'), R.join(','))(comments);

      deleteMultipleComments({ comment_ids })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认删除这 ${comments.length} 条评论？`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
