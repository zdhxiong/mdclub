import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import { unescape } from 'html-escaper';
import {
  get as getTopic,
  getArticles,
  getQuestions,
} from 'mdclub-sdk-js/es/TopicApi';
import commonActions from '~/utils/actionsAbstract';
import userPopoverActions from '~/components/user-popover/actions';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import stateDefault, { tabs } from './stateDefault';

const TABNAME_QUESTIONS = 'questions';
// const TABNAME_ARTICLES = 'articles';

let tab;
let tabIndex = tabs.indexOf(TABNAME_QUESTIONS);
let scroll_position;
const include = ['user', 'topics', 'is_following'];
const per_page = 20;

const as = {
  /**
   * 加载提问列表或文字列表
   */
  getContexts: ({ tabName, page }) => (state) => {
    const params = {
      topic_id: state.topic_id,
      page,
      per_page,
      include,
      order: state[`${tabName}_order`],
    };

    if (tabName === TABNAME_QUESTIONS) {
      return getQuestions(params);
    }

    return getArticles(params);
  },

  onCreate: ({ topic_id }) => (state, actions) => {
    emit('route_update');

    tab = new mdui.Tab('.mc-tab');
    tabIndex = tab.activeIndex;

    if (state.topic_id !== topic_id) {
      actions.setState(stateDefault);
      actions.setState({ topic_id });
      actions.loadTopic();
      actions.afterChangeTab();
    }

    tab.$element.on('change.mdui.tab', () => {
      tabIndex = tab.activeIndex;
      window.scrollTo(0, 0);
      window.history.replaceState({}, '', `#${tabs[tabIndex]}`);
      actions.afterChangeTab();
    });

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

  loadTopic: () => (state, actions) => {
    // 从页面中加载话题数据
    const loadFromPage = () => {
      const topic = window.G_TOPIC;

      if (topic) {
        actions.setState({ topic });
        window.G_TOPIC = null;
        actions.setTitle(unescape(topic.name));
      }

      return topic;
    };

    // ajax 加载话题数据
    const loadFromServer = () => {
      actions.setState({ loading: true });

      getTopic({
        topic_id: state.topic_id,
        include: ['is_following'],
      })
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then(({ data }) => {
          actions.setState({ topic: data });
          actions.setTitle(unescape(data.name));
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[tabIndex];
    const TAB_NAME = tabName.toUpperCase();

    actions.setState({ current_tab: tabName });

    if (state[`${tabName}_pagination`]) {
      return;
    }

    // 从页面中加载初始数据
    if (window[`G_TOPIC_${TAB_NAME}`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_TOPIC_${TAB_NAME}`].data,
        [`${tabName}_pagination`]: window[`G_TOPIC_${TAB_NAME}`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_TOPIC_${TAB_NAME}`] = null;

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
      [`${tabName}_loading`]: true,
    });

    actions
      .getContexts({ tabName, page: 1 })
      .finally(() => {
        actions.setState({ [`${tabName}_loading`]: false });
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
    const tabName = tabs[tabIndex];

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

    actions.setState({ [`${tabName}_loading`]: true });

    actions
      .getContexts({ tabName, page: pagination.page + 1 })
      .finally(() => {
        actions.setState({ [`${tabName}_loading`]: false });
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
   * 点击链接后，保存最后访问的提问ID和提问详情
   * @param context 提问或文字信息
   */
  afterItemClick: (context) => (_, actions) => {
    const tabName = tabs[tabIndex];

    scroll_position = window.pageYOffset;

    if (tabName === TABNAME_QUESTIONS) {
      window.G_QUESTION = context;
      actions.setState({ last_visit_question_id: context.question_id });
    } else {
      window.G_ARTICLE = context;
      actions.setState({ last_visit_article_id: context.article_id });
    }
  },

  /**
   * 切换排序方式
   */
  changeOrder: (order) => (state, actions) => {
    const tabName = tabs[tabIndex];

    if (order === state[`${tabName}_order`]) {
      return;
    }

    actions.setState({
      [`${tabName}_order`]: order,
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: null,
    });

    actions.afterChangeTab();
  },
};

export default extend(as, commonActions, userPopoverActions);
