import { unescape } from 'html-escaper';
import extend from 'mdui.jq/es/functions/extend';
import { get as getArticle } from 'mdclub-sdk-js/es/ArticleApi';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import commonActions from '~/utils/actionsAbstract';
import voteActions from '~/components/vote/actions';
import userPopoverActions from '~/components/user-popover/actions';
import stateDefault from './stateDefault';

const as = {
  onCreate: ({ article_id }) => (state, actions) => {
    emit('route_update');

    if (state.article_id !== article_id) {
      actions.setState(stateDefault);
      actions.setState({ article_id });
      actions.loadArticle();
    }
  },

  loadArticle: () => (state, actions) => {
    // 从页面中加载文章数据
    const loadFromPage = () => {
      const article = window.G_ARTICLE;

      if (article) {
        actions.setState({ article });
        window.G_ARTICLE = null;
        actions.setTitle(unescape(article.title));
      }

      return article;
    };

    // ajax 加载文章数据
    const loadFromServer = () => {
      actions.setState({ loading: true });

      getArticle({
        article_id: state.article_id,
        include: ['user', 'topics', 'is_following', 'voting'],
      })
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then(({ data }) => {
          actions.setState({ article: data });
          actions.setTitle(unescape(data.title));
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },
};

export default extend(as, commonActions, voteActions, userPopoverActions);
