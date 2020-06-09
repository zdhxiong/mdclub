import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import { getList } from 'mdclub-sdk-js/es/TopicApi';
import { getMyFollowingTopics } from 'mdclub-sdk-js/es/UserApi';
import currentUser from '~/utils/currentUser';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import { tabs } from './state';

const TABNAME_FOLLOWING = 'following';
const TABNAME_RECOMMENDED = 'recommended';

let tab; // tab 选项卡实例。若未登录，则没有 tab
let scroll_position;
const is_updated = {
  [TABNAME_FOLLOWING]: false,
  [TABNAME_RECOMMENDED]: false,
};
const include = ['is_following'];

const per_page = 20;
const getTopics = (tabName, page) => {
  if (tabName === TABNAME_FOLLOWING) {
    return getMyFollowingTopics({ page, per_page, include });
  }

  return getList({ page, per_page, include, order: '-follower_count' });
};

const as = {
  /**
   * 初始化
   */
  onCreate: () => (state, actions) => {
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

    if (tabName === TABNAME_FOLLOWING) {
      actions.setTitle('我关注的话题');
    } else {
      actions.setTitle('推荐话题');
    }

    actions.setState({
      current_tab: tabName,
    });

    if (!is_updated[tabName] && state[`${tabName}_pagination`]) {
      return;
    }

    is_updated[tabName] = false;

    // 从页面中读取初始数据
    if (window[`G_TOPICS_${TAB_NAME}`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_TOPICS_${TAB_NAME}`].data,
        [`${tabName}_pagination`]: window[`G_TOPICS_${TAB_NAME}`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_TOPICS_${TAB_NAME}`] = null;

      // 若第一页没有填满屏幕，则加载第二页
      setTimeout(() => {
        actions.infiniteLoad();
      });

      return;
    }

    // ajax 加载初始数据
    actions.setState({
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: null,
    });

    actions.loadStart(tabName);

    getTopics(tabName, 1)
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

    getTopics(tabName, pagination.page + 1)
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
   * 切换到精选话题
   */
  toRecommended: () => {
    tab.show(tabs.indexOf(TABNAME_RECOMMENDED));
  },

  /**
   * 切换到已关注话题
   */
  toFollowing: () => {
    tab.show(tabs.indexOf(TABNAME_FOLLOWING));
  },

  /**
   * 在其他页面关注了话题后，需要调用该方法，以使下次切换到该页面时重新加载关注列表
   */
  followUpdate: () => {
    is_updated[TABNAME_FOLLOWING] = true;
    is_updated[TABNAME_RECOMMENDED] = true;
  },

  /**
   * 点击链接后，保存话题详情
   * @param topic
   */
  afterItemClick: (topic) => {
    window.G_TOPIC = topic;
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
