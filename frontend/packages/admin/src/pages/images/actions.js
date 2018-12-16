import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import 'photoswipe/dist/photoswipe.css';
import 'photoswipe/dist/default-skin/default-skin.css';
import PhotoSwipe from 'photoswipe';
import PhotoSwipeUi_Default from 'photoswipe/dist/photoswipe-ui-default';
import { Image } from 'mdclub-sdk-js';
import { resizeImage } from './helper';
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

    const { searchBar } = global_actions.lazyComponents;

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
    const photoSwipeItems = [];
    const thumbData = resizeImage(response.data);

    response.data.map((item, index) => {
      // 取消选中所有图片
      isCheckedRows[item.hash] = false;

      // 供 PhotoSwipe 插件使用的图片数据数组
      photoSwipeItems.push({
        src: item.urls.o,
        w: item.width,
        h: item.height,
        msrc: item.urls.r,
        title: item.filename,
        author: item.relationship.user.username,
      });
    });

    actions.setState({
      isCheckedRows,
      isCheckedAll: false,
      checkedCount: 0,
      data: response.data,
      photoSwipeItems,
      thumbData,
    });

    global_actions.lazyComponents.pagination.setState(response.pagination);

    const resize = () => {
      window.requestAnimationFrame(() => {
        actions.setState({
          thumbData: resizeImage(response.data),
        });
      });
    };

    $(window).off('resize', resize);
    setTimeout(() => $(window).on('resize', resize));
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

  /**
   * 打开图片
   */
  openImage: ({ item, index }) => (state) => {
    const gallery = new PhotoSwipe($('.pswp')[0], PhotoSwipeUi_Default, state.photoSwipeItems, {
      index,
      loop: false,
      getThumbBoundsFn: (i) => {
        const offset = $('#page-images .mdui-grid-tile').eq(i).find('.image').offset();

        return {
          x: offset.left,
          y: offset.top,
          w: offset.width,
        };
      },
    });
    gallery.init();
  },

  /**
   * 点击一张图片
   */
  clickImage: ({ e, item, index }) => (state, actions) => {
    // 点在放大图标上，不执行下面的代码
    if (e.target.nodeName === 'I') {
      return;
    }

    if (state.checkedCount) {
      // 已有图片被选中，则切换图片的选中状态
      actions.checkOne(item.hash);
    } else {
      // 没有图片被选中，则放大图片
      actions.openImage({ item, index });
    }
  },
});
