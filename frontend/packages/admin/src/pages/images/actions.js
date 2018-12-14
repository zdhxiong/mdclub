import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Image } from 'mdclub-sdk-js';
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
      dialogUser,
      dialogArticle,
      dialogQuestion,
      dialogAnswer,
    } = global_actions.lazyComponents;

    const searchBarState = {
      fields: [
        {
          name: 'user_id',
          label: '用户ID',
        },
        {
          name: 'item_type',
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
      isNeedRender: true,
    };

    searchBar.setState(searchBarState);

    $(document).on('search-submit', actions.loadData);
    actions.loadData();
  },

  /**
   * 销毁前
   */
  destroy: () => (state, actions) => {
    $(document).off('search-submit');

    actions.setState({
      data: [],
      loading: false,
      isCheckedRows: {},
      isCheckedAll: false,
      checkedCount: 0,
    });
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const { pagination, searchBar } = global_actions.lazyComponents;

    actions.loadStart();

    const data = $.extend({}, ObjectHelper.filter(searchBar.getState().data), {
      page: pagination.getState().page,
      per_page: pagination.getState().per_page,
    });

    Image.getList(data, actions.loadEnd);
  },

  /**
   * 开始加载数据
   */
  loadStart: () => (state, actions) => {
    actions.setState({
      data: [],
      loading: true,
    });
  },

  /**
   * 数据加载完成
   */
  loadEnd: response => (state, actions) => {
    actions.setState({ loading: false });

    if (response.code) {
      mdui.snackbar(response.message);
      return;
    }

    const isCheckedRows = {};
    response.data.map((item) => {
      isCheckedRows[item.hash] = false;
    });

    actions.setState({
      isCheckedRows,
      isCheckedAll: false,
      checkedCount: 0,
      data: response.data,
    });

    global_actions.lazyComponents.pagination.setState(response.pagination);
  },

  /**
   * 切换一张图片的选中状态
   */
  checkOne: hash => (state, actions) => {
    const { isCheckedRows } = state;
    isCheckedRows[hash] = !isCheckedRows[hash];

    let checkedCount = 0;
    let isCheckedAll = true;

    Object.keys(isCheckedRows).map((_hash) => {
      if (!isCheckedRows[_hash]) {
        isCheckedAll = false;
      } else {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },

  /**
   * 切换全部图片的选中状态
   */
  checkAll: e => (state, actions) => {
    let checkedCount = 0;
    const isCheckedAll = e.target.checked;
    const { isCheckedRows } = state;

    Object.keys(isCheckedRows).map((_rowId) => {
      isCheckedRows[_rowId] = isCheckedAll;

      if (isCheckedAll) {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },
});
