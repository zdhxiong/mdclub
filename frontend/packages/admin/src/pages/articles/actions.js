import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getArticles,
  del as deleteArticle,
  deleteMultiple as deleteMultipleArticles,
} from 'mdclub-sdk-js/es/ArticleApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('文章管理');

    emit('searchbar_state_update', {
      fields: [
        { name: 'article_id', label: '文章ID' },
        { name: 'user_id', label: '用户ID' },
      ],
      data: {
        article_id: '',
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
          title: '标题',
          field: 'title',
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
          type: 'target',
          getTargetLink: ({ article_id }) => {
            return `${window.G_ROOT}/articles/${article_id}`;
          },
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
        { name: '上次更新时间', value: '-update_time' },
        { name: '投票数', value: '-vote_count' },
      ],
      order: '-create_time',
      primaryKey: 'article_id',
      onRowClick: (article) => {
        emit('article_open', article);
        emit('datatable_state_update', {
          lastVisitId: article.article_id,
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

    searchData.include = ['user', 'topics'];

    datatable.loadStart();

    getArticles(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定文章
   */
  editOne: (article) => {
    emit('article_edit_open', article);
  },

  /**
   * 删除指定文章
   */
  deleteOne: ({ article_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      deleteArticle({ article_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认删除该文章？', confirm, () => {}, options);
  },

  /**
   * 批量删除文章
   */
  batchDelete: (articles) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const article_ids = R.pipe(R.pluck('article_id'), R.join(','))(articles);

      deleteMultipleArticles({ article_ids })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认删除这 ${articles.length} 篇文章？`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
