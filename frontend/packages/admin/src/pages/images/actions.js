import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Image } from 'mdclub-sdk-js';
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
          name: 'hash',
          label: 'hash',
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
        {
          name: 'user_id',
          label: '用户ID',
        },
      ],
      data: {
        hash: '',
        item_type: '',
        item_id: '',
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
    datatableActions.loadStart();

    const paginationState = paginationActions.getState();

    const data = {
      page: paginationState.page,
      per_page: paginationState.per_page,
    };

    Image.getList(data, () => {

    });
  },
});
