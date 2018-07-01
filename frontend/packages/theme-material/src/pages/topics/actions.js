import mdui from 'mdui';
import $ from 'mdui.JQ';
import TopicService from '../../service/Topic';
import TopicFollowService from '../../service/TopicFollow';

const TAB_INDEX = {
  FOLLOWING: 0,
  RECOMMENDED: 1,
};
const tabs = ['following', 'recommended'];
let tabIndex;
let Tab;
let global_actions;
let scroll_position;
const is_updated = {
  [TAB_INDEX.FOLLOWING]: false,
  [TAB_INDEX.RECOMMENDED]: false,
};

export default {
  setState: value => (value),
  getState: () => state => state,

  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.theme.setPrimary('blue-grey');
    global_actions.routeChange(window.location.pathname);
    tabIndex = TAB_INDEX.RECOMMENDED;

    // 登录用户有 Tab 切换，未登录用户直接显示推荐话题
    if (global_actions.getState().user.user.user_id) {
      Tab = new mdui.Tab('#tab-topics');
      tabIndex = Tab.activeIndex;

      Tab.$tab.on('change.mdui.tab', (event) => {
        tabIndex = event._detail.index;
        window.scrollTo(0, 0);
        history.replaceState({}, '', `#${tabs[tabIndex]}`);
        actions.afterChangeTab();
      });
    }

    actions.afterChangeTab();

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }

    $(window).on('scroll', actions.infiniteLoad);
  },

  /**
   * 切换 Tab 之后
   */
  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[tabIndex];

    actions.setState({
      current_tab: tabName,
    });

    if (!is_updated[tabIndex] && state[`${tabName}_pagination`]) {
      return;
    }

    is_updated[tabIndex] = false;

    // 从页面中读取初始数据
    if (window[`G_${tabName.toUpperCase()}_TOPICS`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_${tabName.toUpperCase()}_TOPICS`].data,
        [`${tabName}_pagination`]: window[`G_${tabName.toUpperCase()}_TOPICS`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_${tabName.toUpperCase()}_TOPICS`] = false;

      return;
    }

    // ajax 加载初始数据
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
        TopicFollowService.getMyFollowingTopics({}, loaded);
        break;

      case TAB_INDEX.RECOMMENDED:
        if (global_actions.getState().user.user.user_id) {
          TopicFollowService.getMyNotFollowingTopics({}, loaded);
        } else {
          TopicService.getList({}, loaded);
        }
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
    const state = actions.getState();
    const pagination = state[`${tabName}_pagination`];

    if (state[`${tabName}_loading`] || !pagination) {
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
        TopicFollowService.getMyFollowingTopics(data, loaded);
        break;

      case TAB_INDEX.RECOMMENDED:
        if (global_actions.getState().user.user.user_id) {
          TopicFollowService.getMyNotFollowingTopics(data, loaded);
        } else {
          TopicService.getList(data, loaded);
        }
        break;

      default:
        break;
    }
  },

  /**
   * 切换关注状态
   */
  toggleFollow: topic_id => (state, actions) => {
    if (!global_actions.getState().user.user.user_id) {
      global_actions.components.login.open();

      return;
    }

    const tabName = tabs[tabIndex];
    const data = actions.getState()[`${tabName}_data`];

    data.forEach((topic, index) => {
      if (topic.topic_id !== topic_id) {
        return;
      }

      data[index].relationship.is_following = !topic.relationship.is_following;
      actions.setState({
        [`${tabName}_data`]: data,
      });

      const done = (response) => {
        if (!response.code) {
          is_updated[TAB_INDEX.FOLLOWING] = true;
          is_updated[TAB_INDEX.RECOMMENDED] = true;

          return;
        }

        mdui.snackbar(response.message);

        data[index].relationship.is_following = !topic.relationship.is_following;
        actions.setState({
          [`${tabName}_data`]: data,
        });
      };

      if (topic.relationship.is_following) {
        TopicFollowService.addFollow(topic_id, done);
      } else {
        TopicFollowService.deleteFollow(topic_id, done);
      }
    });
  },

  /**
   * 切换到精选话题
   */
  toRecommended: () => {
    Tab.show(TAB_INDEX.RECOMMENDED);
  },

  /**
   * 切换到已关注话题
   */
  toFollowing: () => {
    Tab.show(TAB_INDEX.FOLLOWING);
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
