import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Article } from 'mdclub-sdk-js';
import ObjectHelper from '../../helper/obj';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

const searchState = {
  fields: [
    {
      name: 'article_id',
      label: '文章ID',
    },
    {
      name: 'user_id',
      label: '用户ID',
    },
  ],
  data: {
    article_id: '',
    user_id: '',
  },
  isDataEmpty: true,
};

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange();
    global_actions = props.global_actions;
    global_actions.lazyComponents.searchBar.setState(searchState);

    $(document).on('search-submit', () => {
      actions.loadData();
    });

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
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;

    datatableActions.setState({ order: '-create_time' });
    datatableActions.loadStart();

    const datatableState = datatableActions.getState();
    const paginationState = global_actions.lazyComponents.pagination.getState();
    const searchBarData = global_actions.lazyComponents.searchBar.getState().data;

    const data = $.extend({}, ObjectHelper.filter(searchBarData), {
      page: paginationState.page,
      per_page: paginationState.per_page,
      order: datatableState.order,
    });

    const success = (response) => {
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
    };

    Article.getList(data, success);
  },

  /**
   * 编辑指定文章
   */
  editOne: article => (state, actions) => {

  },

  /**
   * 删除指定文章
   */
  deleteOne: ({ article_id }) => (state, actions) => {
    const confirm = () => {
      $.loadStart();
      Article.deleteOne(article_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复该文章', '确认删除该文章', confirm, false, options);
  },

  /**
   * 批量删除文章
   */
  batchDelete: articles => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const article_ids = [];
      articles.map((article) => {
        article_ids.push(article.article_id);
      });

      Article.deleteMultiple(article_ids.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些文章', `确认删除这 ${articles.length} 篇文章`, confirm, false, options);
  },
});
