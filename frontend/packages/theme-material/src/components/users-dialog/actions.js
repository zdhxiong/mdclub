import { location } from 'hyperapp-router';
import $ from 'mdui.jq';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { getFollowees, getFollowers } from 'mdclub-sdk-js/es/UserApi';
import { getFollowers as getArticleFollowers } from 'mdclub-sdk-js/es/ArticleApi';
import { getFollowers as getQuestionFollowers } from 'mdclub-sdk-js/es/QuestionApi';
import { getFollowers as getTopicFollowers } from 'mdclub-sdk-js/es/TopicApi';
import commonActions from '~/utils/actionsAbstract';
import stateDefault from '~/components/users-dialog/stateDefault';
import { fullPath } from '~/utils/path';
import apiCatch from '~/utils/errorHandler';

let dialog;
let $dialog;
let $content;

const service = {
  followees: getFollowees,
  followers: getFollowers,
  topic_followers: getTopicFollowers,
  question_followers: getQuestionFollowers,
  article_followers: getArticleFollowers,
};

const paramField = {
  followees: 'user_id',
  followers: 'user_id',
  topic_followers: 'topic_id',
  question_followers: 'question_id',
  article_followers: 'article_id',
};

const as = {
  onCreate: ({ element }) => {
    $dialog = $(element);
    $content = $dialog.find('.mdui-dialog-content');
  },

  /**
   * 打开对话框
   * @param props
   * {type: string, id: int}
   * 用户列表类型 followees, followers, topic_followers, question_followers, article_followers
   */
  open: ({ type, id }) => (state, actions) => {
    if (!dialog) {
      dialog = new mdui.Dialog('.mc-users-dialog', {
        history: false,
      });

      dialog.$element.on('closed.mdui.dialog', () => {
        actions.setState(stateDefault);
      });
    }

    actions.setState({
      type,
      data: [],
      pagination: null,
      loading: true,
    });

    dialog.open();

    const loaded = (promise) => {
      promise
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then(({ data, pagination }) => {
          actions.setState({
            data: actions.getState().data.concat(data),
            pagination,
          });
        })
        .catch(apiCatch);
    };

    const infiniteLoad = () => {
      if (actions.getState().loading) {
        return;
      }

      const { pagination } = actions.getState();

      if (!pagination) {
        return;
      }

      if (pagination.page >= pagination.pages) {
        return;
      }

      if (
        $content[0].scrollHeight -
          $content[0].scrollTop -
          $content[0].offsetHeight >
        100
      ) {
        return;
      }

      actions.setState({ loading: true });

      loaded(
        service[type]({
          [paramField[type]]: id,
          page: pagination.page + 1,
          include: ['is_following', 'is_me'],
        }),
      );
    };

    loaded(
      service[type]({
        [paramField[type]]: id,
        include: ['is_following', 'is_me'],
      }),
    );

    $content.on('scroll', infiniteLoad);

    $dialog.on('close.mdui.dialog', () => {
      $content.off('scroll', infiniteLoad);
    });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    if (dialog) {
      dialog.close();
    }
  },

  /**
   * 点击 item
   */
  onItemClick: (user_id) => {
    location.actions.go(fullPath(`/users/${user_id}`));
    dialog.close();
  },
};

export default extend(as, commonActions);
