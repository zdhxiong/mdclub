import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Image } from 'mdclub-sdk-js';
import ObjectHelper from '../../helper/obj';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

const searchState = {
  fields: [
    {
      name: 'hash',
      label: 'hash',
    },
    {
      name: 'user_id',
      label: '用户ID',
    },
    {
      name: 'item_type',
      label: '类型',
      enum: [
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
      name: 'item_id',
      label: '类型ID',
    },
  ],
  data: {
    hash: '',
    item_type: '',
    item_id: '',
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

    datatableActions.loadStart();

    const paginationState = global_actions.lazyComponents.pagination.getState();
    const searchBarData = global_actions.lazyComponents.searchBar.getState().data;

    const data = $.extend({}, ObjectHelper.filter(searchBarData), {
      page: paginationState.page,
      per_page: paginationState.per_page,
    });

    Image.getList(data, () => {

    });
  },
});
