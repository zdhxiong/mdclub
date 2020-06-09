import { location } from 'hyperapp-router';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import { getList, create as createArticle } from 'mdclub-sdk-js/es/ArticleApi';
import { getMyFollowingArticles } from 'mdclub-sdk-js/es/UserApi';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import commonActions from '~/utils/actionsAbstract';
import editorActions from '~/components/editor/actions';
import topicSelectorActions from '~/components/editor/components/topic-selector/actions';
import userPopoverActions from '~/components/user-popover/actions';
import { emit } from '~/utils/pubsub';
import { tabs } from './state';
import { fullPath } from '~/utils/path';
import apiCatch from '~/utils/errorHandler';
import { currentTimestamp } from '~/utils/time';

const TABNAME_RECENT = 'recent';
const TABNAME_POPULAR = 'popular';
const TABNAME_FOLLOWING = 'following';

let tab;
let tabIndex = tabs.indexOf(TABNAME_RECENT);
let scroll_position;
const is_updated = {
  [TABNAME_RECENT]: false, // 最近更新列表是否有更新
  [TABNAME_POPULAR]: false, // 最近热门列表是否有更新
  [TABNAME_FOLLOWING]: false, // 我关注的列表是否有更新
};
// 上次更新的时间，若当前时间距离上次更新时间超过一定范围，则重新加载列表
const last_update_time = {
  [TABNAME_RECENT]: 0,
  [TABNAME_POPULAR]: 0,
  [TABNAME_FOLLOWING]: 0,
};
const include = ['user', 'topics', 'is_following', 'voting'];
const per_page = 20;
const getArticles = (tabName, page) => {
  if (tabName === TABNAME_RECENT) {
    return getList({ page, include, per_page, order: '-update_time' });
  }

  if (tabName === TABNAME_POPULAR) {
    return getList({ page, include, per_page, order: '-vote_count' });
  }

  return getMyFollowingArticles({ page, include, per_page });
};

const as = {
  onCreate: () => (_, actions) => {
    emit('route_update');

    tab = new mdui.Tab('.mc-tab');
    tabIndex = tab.activeIndex;

    tab.$element.on('change.mdui.tab', () => {
      tabIndex = tab.activeIndex;
      window.scrollTo(0, 0);
      window.history.replaceState({}, '', `#${tabs[tabIndex]}`);
      actions.afterChangeTab();
    });

    actions.afterChangeTab();

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }

    // 绑定加载更多
    $window.on('scroll', actions.infiniteLoad);
  },

  /**
   * 切换 Tab 之后
   */
  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[tabIndex];
    const TAB_NAME = tabName.toUpperCase();

    if (tabName === TABNAME_RECENT) {
      actions.setTitle('最新文章');
    } else if (tabName === TABNAME_POPULAR) {
      actions.setTitle('近期热门文章');
    } else {
      actions.setTitle('我关注的文章');
    }

    actions.setState({ current_tab: tabName });

    if (
      !is_updated[tabs[tabIndex]] &&
      state[`${tabName}_pagination`] &&
      currentTimestamp() - last_update_time[tabs[tabIndex]] < 180
    ) {
      return;
    }

    is_updated[tabs[tabIndex]] = false;
    last_update_time[tabs[tabIndex]] = currentTimestamp();

    // 从页面中加载初始数据
    if (window[`G_ARTICLES_${TAB_NAME}`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_ARTICLES_${TAB_NAME}`].data,
        [`${tabName}_pagination`]: window[`G_ARTICLES_${TAB_NAME}`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_ARTICLES_${TAB_NAME}`] = null;

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

    getArticles(tabName, 1)
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

    getArticles(tabName, pagination.page + 1)
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
   * 切换到最近更新
   */
  toRecent: () => {
    tab.show(tabs.indexOf(TABNAME_RECENT));
  },

  /**
   * 切换到近期热门
   */
  toPopular: () => {
    tab.show(tabs.indexOf(TABNAME_POPULAR));
  },

  /**
   * 切换到我关注的
   */
  toFolliwing: () => {
    tab.show(tabs.indexOf(TABNAME_FOLLOWING));
  },

  /**
   * 在其他页面关注了问题后，需要调用该方法，以使下次切换到该页面时重新加载关注列表
   */
  followUpdate: () => {
    is_updated[TABNAME_FOLLOWING] = true;
  },

  /**
   * 文章信息更新后（发布评论，关注），需要调用该方法
   */
  articleUpdate: (article) => (state, actions) => {
    let articles = [];

    tabs.forEach((tabName) => {
      articles = state[`${tabName}_data`];

      let hasUpdate = false;
      articles.forEach((_article, i) => {
        if (article.article_id === _article.article_id) {
          hasUpdate = true;
          articles[i] = article;
        }
      });

      if (hasUpdate) {
        actions.setState({
          [`${tabName}_data`]: articles,
        });
      }
    });
  },

  /**
   * 发布提问
   */
  publish: ({ title, content }) => (state, actions) => {
    const { editor_selected_topic_ids: topic_ids, auto_save_key } = state;

    if (!title) {
      mdui.snackbar('请输入标题');
      return;
    }

    if (!topic_ids.length) {
      mdui.snackbar('请选择话题');
      return;
    }

    if (!content || content === '<p><br></p>') {
      mdui.snackbar('请输入正文');
      return;
    }

    actions.setState({ publishing: true });

    createArticle({
      title,
      topic_ids,
      content_rendered: content,
      include,
    })
      .finally(() => {
        actions.setState({ publishing: false });
      })
      .then((response) => {
        is_updated[TABNAME_RECENT] = true;
        is_updated[TABNAME_POPULAR] = true;
        is_updated[TABNAME_FOLLOWING] = true;

        // 清空草稿和编辑器内容
        window.localStorage.removeItem(`${auto_save_key}-title`);
        window.localStorage.removeItem(`${auto_save_key}-topics`);
        window.localStorage.removeItem(`${auto_save_key}-content`);

        actions.editorClose();

        // 到文章详情页
        window.G_ARTICLE = response.data;
        location.actions.go(fullPath(`/articles/${response.data.article_id}`));
      })
      .catch((response) => {
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          mdui.snackbar(Object.values(response.errors)[0]);
          return;
        }

        apiCatch(response);
      });
  },

  /**
   * 点击链接后，保存最后访问的文章ID和文章详情
   * @param article
   */
  afterItemClick: (article) => (_, actions) => {
    window.G_ARTICLE = article;
    scroll_position = window.pageYOffset;

    actions.setState({
      last_visit_id: article.article_id,
    });
  },
};

export default extend(
  as,
  commonActions,
  editorActions,
  topicSelectorActions,
  userPopoverActions,
);
