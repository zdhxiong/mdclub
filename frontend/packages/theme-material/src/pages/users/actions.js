import mdui, { JQ as $ } from 'mdui';
import { User } from 'mdclub-sdk-js';

const TAB_INDEX = {
  FOLLOWING: 0,
  FOLLOWERS: 1,
  RECOMMENDED: 2,
};
const tabs = ['following', 'followers', 'recommended'];
let tabIndex;
let Tab;
let global_actions;
let scroll_position;
const is_updated = {
  [TAB_INDEX.FOLLOWING]: false, // 已关注列表是否有更新，有更新则切换到该选项卡时需要重新加载
  [TAB_INDEX.FOLLOWERS]: false, // 关注者列表是否有更新
  [TAB_INDEX.RECOMMENDED]: false, // 推荐列表是否有更新
};

export default {
  setState: value => (value),
  getState: () => state => state,
  setTitle: (title) => {
    $('title').text(title);
  },

  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.theme.setPrimary('');
    global_actions.routeChange(window.location.pathname);

    Tab = new mdui.Tab('#tab-users');
    tabIndex = Tab.activeIndex;

    Tab.$tab.on('change.mdui.tab', (event) => {
      tabIndex = event._detail.index;
      window.scrollTo(0, 0);
      history.replaceState({}, '', `#${tabs[tabIndex]}`);
      actions.afterChangeTab();
    });

    actions.afterChangeTab();

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }

    // 绑定加载更多
    $(window).on('scroll', actions.infiniteLoad);
  },

  /**
   * 切换 Tab 之后
   */
  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[tabIndex];

    switch (tabName) {
      case 'following':
        actions.setTitle('我关注的人');
        break;
      case 'followers':
        actions.setTitle('我的关注者');
        break;
      case 'recommended':
        actions.setTitle('推荐人脉');
        break;
      default:
        actions.setTitle('推荐人脉');
        break;
    }

    actions.setState({
      current_tab: tabName,
    });

    if (!is_updated[tabIndex] && state[`${tabName}_pagination`]) {
      return;
    }

    is_updated[tabIndex] = false;

    // 从页面中加载初始数据
    if (window[`G_${tabName.toUpperCase()}_USERS`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_${tabName.toUpperCase()}_USERS`].data,
        [`${tabName}_pagination`]: window[`G_${tabName.toUpperCase()}_USERS`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_${tabName.toUpperCase()}_USERS`] = false;

      return;
    }

    // ajax 加载数据
    actions.setState({
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: false,
    });

    actions.loadStart(tabName);

    const loaded = (response) => {
      actions.loadEnd(tabName);

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        [`${tabName}_data`]: response.data,
        [`${tabName}_pagination`]: response.pagination,
      });
    };

    switch (tabIndex) {
      case TAB_INDEX.FOLLOWING:
        User.getMyFollowing({}, loaded);
        break;

      case TAB_INDEX.FOLLOWERS:
        User.getMyFollowers({}, loaded);
        break;

      case TAB_INDEX.RECOMMENDED:
        User.getMyNotFollowing({}, loaded);
        break;

      default:
        break;
    }
  },

  /**
   * 绑定下拉加载更多
   */
  infiniteLoad: () => (_, actions) => {
    const tabName = tabs[tabIndex];

    if (actions.getState()[`${tabName}_loading`]) {
      return;
    }

    const pagination = actions.getState()[`${tabName}_pagination`];

    if (!pagination) {
      return;
    }

    if (pagination.page >= pagination.total_page) {
      return;
    }

    if (document.body.scrollHeight - window.pageYOffset - window.innerHeight > 100) {
      return;
    }

    actions.loadStart(tabName);

    const loaded = (response) => {
      actions.loadEnd(tabName);

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        [`${tabName}_data`]: actions.getState()[`${tabName}_data`].concat(response.data),
        [`${tabName}_pagination`]: response.pagination,
      });
    };

    const data = {
      page: pagination.page + 1,
    };

    switch (tabIndex) {
      case TAB_INDEX.FOLLOWING:
        User.getMyFollowing(data, loaded);
        break;

      case TAB_INDEX.FOLLOWERS:
        User.getMyFollowers(data, loaded);
        break;

      case TAB_INDEX.RECOMMENDED:
        User.getMyNotFollowing(data, loaded);
        break;

      default:
        break;
    }
  },

  /**
   * 切换关注状态
   */
  toggleFollow: user_id => (state, actions) => {
    const tabName = tabs[tabIndex];
    const data = actions.getState()[`${tabName}_data`];

    data.forEach((user, index) => {
      if (user.user_id !== user_id) {
        return;
      }

      data[index].relationship.is_following = !user.relationship.is_following;
      actions.setState({
        [`${tabName}_data`]: data,
      });

      const done = (response) => {
        if (!response.code) {
          actions.followUpdate();

          return;
        }

        mdui.snackbar(response.message);

        data[index].relationship.is_following = user.relationship.is_following;
        actions.setState({
          [`${tabName}_data`]: data,
        });
      };

      if (user.relationship.is_following) {
        User.addFollow(user_id, done);
      } else {
        User.deleteFollow(user_id, done);
      }
    });
  },

  /**
   * 切换到推荐用户
   */
  toRecommended: () => {
    Tab.show(TAB_INDEX.RECOMMENDED);
  },

  /**
   * 在其他页面关注了用户后，需要调用该方法，以使下次切换到该页面时重新加载关注列表
   */
  followUpdate: () => {
    is_updated[TAB_INDEX.FOLLOWING] = true;
    is_updated[TAB_INDEX.RECOMMENDED] = true;
  },

  /**
   * 保存滚动条位置
   */
  saveScrollPosition: () => {
    scroll_position = window.pageYOffset;
  },

  /**
   * 开始加载
   */
  loadStart: tabName => ({
    [`${tabName}_loading`]: true,
  }),

  /**
   * 结束加载
   */
  loadEnd: tabName => ({
    [`${tabName}_loading`]: false,
  }),
};
