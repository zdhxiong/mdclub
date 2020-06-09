import extend from 'mdui.jq/es/functions/extend';
import { getList as getTopics } from 'mdclub-sdk-js/es/TopicApi';
import { getList as getQuestions } from 'mdclub-sdk-js/es/QuestionApi';
import { getList as getArticles } from 'mdclub-sdk-js/es/ArticleApi';
import { isUndefined } from 'mdui.jq/es/utils';
import commonActions from '~/utils/actionsAbstract';
import userPopoverActions from '~/components/user-popover/actions';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';

let scroll_position;

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('');

    if (!state.topics_pagination) {
      actions.loadData({
        localParamName: 'G_INDEX_TOPICS',
        func: getTopics,
        fieldPrefix: 'topics',
        include: ['is_following'],
        order: '-follower_count',
        per_page: 12,
      });
    }

    const include = ['user', 'topics', 'is_following'];

    if (!state.questions_recent_pagination) {
      actions.loadData({
        localParamName: 'G_INDEX_QUESTIONS_RECENT',
        func: getQuestions,
        fieldPrefix: 'questions_recent',
        include,
        order: '-update_time',
        per_page: 5,
      });
    }

    if (!state.questions_popular_pagination) {
      actions.loadData({
        localParamName: 'G_INDEX_QUESTIONS_POPULAR',
        func: getQuestions,
        fieldPrefix: 'questions_popular',
        include,
        order: '-answer_count',
        per_page: 5,
      });
    }

    if (!state.articles_recent_pagination) {
      actions.loadData({
        localParamName: 'G_INDEX_ARTICLES_RECENT',
        func: getArticles,
        fieldPrefix: 'articles_recent',
        include,
        order: '-create_time',
        per_page: 5,
      });
    }

    if (!state.articles_popular_pagination) {
      actions.loadData({
        localParamName: 'G_INDEX_ARTICLES_POPULAR',
        func: getArticles,
        fieldPrefix: 'articles_popular',
        include,
        order: '-vote_count',
        per_page: 5,
      });
    }

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }
  },

  onDestroy: () => {},

  /**
   * 加载话题列表、提问列表、和文章列表
   */
  loadData: ({
    localParamName,
    func,
    fieldPrefix,
    include,
    order,
    per_page,
  }) => (state, actions) => {
    const loadFromPage = () => {
      const response = window[localParamName];

      if (response) {
        actions.setState({
          [`${fieldPrefix}_data`]: response.data,
          [`${fieldPrefix}_pagination`]: response.pagination,
        });
        window[localParamName] = null;
      }

      return response;
    };

    const loadFromServer = () => {
      actions.setState({
        [`${fieldPrefix}_loading`]: true,
      });

      func({
        include,
        order,
        per_page,
      })
        .finally(() => {
          actions.setState({
            [`${fieldPrefix}_loading`]: false,
          });
        })
        .then(({ data, pagination }) => {
          actions.setState({
            [`${fieldPrefix}_data`]: data,
            [`${fieldPrefix}_pagination`]: pagination,
          });
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  afterItemClick: (item) => {
    scroll_position = window.pageYOffset;

    if (!isUndefined(item.topic_id)) {
      window.G_TOPIC = item;
    } else if (!isUndefined(item.question_id)) {
      window.G_QUESTION = item;
    } else if (!isUndefined(item.article_id)) {
      window.G_ARTICLE = item;
    }
  },
};

export default extend(as, commonActions, userPopoverActions);
