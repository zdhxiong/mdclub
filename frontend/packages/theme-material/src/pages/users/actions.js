import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import {
  getList,
  getMyFollowees,
  getMyFollowers,
} from 'mdclub-sdk-js/es/UserApi';
import currentUser from '~/utils/currentUser';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import { tabs } from './state';

const TABNAME_FOLLOWEES = 'followees';
const TABNAME_FOLLOWERS = 'followers';
const TABNAME_RECOMMENDED = 'recommended';

let tab; // tab 选项卡实例。若未登录，则没有 tab
let scroll_position;
const is_updated = {
  [TABNAME_FOLLOWEES]: false, // 已关注列表是否有更新，有更新则切换到该选项卡时需要重新加载
  [TABNAME_FOLLOWERS]: false, // 关注者列表是否有更新
  [TABNAME_RECOMMENDED]: false, // 推荐列表是否有更新
};
const include = ['is_following', 'is_me'];

const per_page = 20;
const getUsers = (tabName, page) => {
  if (tabName === TABNAME_FOLLOWEES) {
    return getMyFollowees({ include, page, per_page });
  }

  if (tabName === TABNAME_FOLLOWERS) {
    return getMyFollowers({ include, page, per_page });
  }

  return getList({ include, page, per_page, order: '-follower_count' });
};

const as = {
  /**
   * 初始化
   */
  onCreate: () => (_, actions) => {
    emit('route_update');

    if (currentUser()) {
      tab = new mdui.Tab('.mc-tab');
      actions.setState({ tabIndex: tab.activeIndex });

      tab.$element.on('change.mdui.tab', () => {
        actions.setState({ tabIndex: tab.activeIndex });
        window.scrollTo(0, 0);
        window.history.replaceState({}, '', `#${tabs[tab.activeIndex]}`);
        actions.afterChangeTab();
      });
    }

    actions.afterChangeTab();

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }

    // 绑定加载更多
    $window.on('scroll', actions.infiniteLoad);
  },

  onDestroy: () => (_, actions) => {
    $window.off('scroll', actions.infiniteLoad);
  },

  /**
   * 切换 Tab 之后
   */
  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[state.tabIndex];
    const TAB_NAME = tabName.toUpperCase();

    if (tabName === TABNAME_FOLLOWEES) {
      actions.setTitle('我关注的人');
    } else if (tabName === TABNAME_FOLLOWERS) {
      actions.setTitle('我的关注者');
    } else {
      actions.setTitle('推荐人脉');
    }

    actions.setState({
      current_tab: tabName,
    });

    if (!is_updated[tabName] && state[`${tabName}_pagination`]) {
      return;
    }

    is_updated[tabName] = false;

    // 从页面中加载初始数据
    if (window[`G_USERS_${TAB_NAME}`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_USERS_${TAB_NAME}`].data,
        [`${tabName}_pagination`]: window[`G_USERS_${TAB_NAME}`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_USERS_${TAB_NAME}`] = null;

      // 若第一页没有填满屏幕，则加载第二页
      setTimeout(() => {
        actions.infiniteLoad();
      });

      return;
    }

    // ajax 加载数据
    actions.setState({
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: null,
    });

    actions.loadStart(tabName);

    getUsers(tabName, 1)
      .finally(() => {
        actions.loadEnd(tabName);
      })
      .then((response) => {
        actions.setState({
          [`${tabName}_data`]: response.data,
          [`${tabName}_pagination`]: response.pagination,
        });

        // 若第一页没有填满屏幕，则加载第二页
        setTimeout(() => {
          actions.infiniteLoad();
        });
      })
      .catch(apiCatch);
  },

  /**
   * 绑定下拉加载更多
   */
  infiniteLoad: () => (state, actions) => {
    const tabName = tabs[state.tabIndex];

    if (state[`${tabName}_loading`]) {
      return;
    }

    const pagination = state[`${tabName}_pagination`];

    if (pagination.page >= pagination.pages) {
      return;
    }

    if (
      document.body.scrollHeight - window.pageYOffset - window.innerHeight >
      100
    ) {
      return;
    }

    actions.loadStart(tabName);

    getUsers(tabName, pagination.page + 1)
      .finally(() => {
        actions.loadEnd(tabName);
      })
      .then((response) => {
        actions.setState({
          [`${tabName}_data`]: state[`${tabName}_data`].concat(response.data),
          [`${tabName}_pagination`]: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 切换到推荐用户
   */
  toRecommended: () => {
    tab.show(tabs.indexOf(TABNAME_RECOMMENDED));
  },

  /**
   * 在其他页面关注了用户后，需要调用该方法，以使下次切换到该页面时重新加载关注列表
   */
  followUpdate: () => {
    is_updated[TABNAME_FOLLOWEES] = true;
    is_updated[TABNAME_RECOMMENDED] = true;
  },

  /**
   * 点击链接后，保存用户信息
   */
  afterItemClick: (user) => {
    window.G_INTERVIEWEE = user;
    scroll_position = window.pageYOffset;
  },

  /**
   * 开始加载
   */
  loadStart: (tabName) => ({
    [`${tabName}_loading`]: true,
  }),

  /**
   * 结束加载
   */
  loadEnd: (tabName) => ({
    [`${tabName}_loading`]: false,
  }),
};

export default extend(as, commonActions);
