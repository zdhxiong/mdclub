import {
  addFollow as addFollowUser,
  deleteFollow as deleteFollowUser,
} from 'mdclub-sdk-js/es/UserApi';
import {
  addFollow as addFollowTopic,
  deleteFollow as deleteFollowTopic,
} from 'mdclub-sdk-js/es/TopicApi';
import {
  addFollow as addFollowArticle,
  deleteFollow as deleteFollowArticle,
} from 'mdclub-sdk-js/es/ArticleApi';
import {
  addFollow as addFollowQuestion,
  deleteFollow as deleteFollowQuestion,
} from 'mdclub-sdk-js/es/QuestionApi';
import currentUser from '~/utils/currentUser';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';

const getAddFollowFunction = (type) => {
  if (!type.indexOf('question')) {
    return addFollowQuestion;
  }

  if (!type.indexOf('user')) {
    return addFollowUser;
  }

  if (!type.indexOf('topic') || type === 'index_topics') {
    return addFollowTopic;
  }

  return addFollowArticle;
};

const getDeleteFollowFunction = (type) => {
  if (!type.indexOf('question')) {
    return deleteFollowQuestion;
  }

  if (!type.indexOf('user')) {
    return deleteFollowUser;
  }

  if (!type.indexOf('topic') || type === 'index_topics') {
    return deleteFollowTopic;
  }

  return deleteFollowArticle;
};

/**
 * article, question, user, topic 切换关注状态
 * @param type
 * @param state
 * @param actions
 */
const toggleOne = (type, state, actions) => {
  const field = type === 'user' ? 'interviewee' : type;
  const field_id = `${type}_id`;
  const following_field = `following_${field}`;

  const { [field]: data, [following_field]: following } = state;

  if (following) {
    return;
  }

  actions.setState({ [following_field]: true });

  // 修改显示的关注状态
  const changeFollow = () => {
    data.relationships.is_following = !data.relationships.is_following;
    actions.setState({ [field]: data });
  };

  // 不等 ajax 的响应，先修改显示状态
  changeFollow();

  const followParams = { [field_id]: data[field_id] };
  const func = data.relationships.is_following
    ? getAddFollowFunction(type)
    : getDeleteFollowFunction(type);

  const promise = func(followParams);

  promise
    .finally(() => actions.setState({ [following_field]: false }))
    .then((response) => {
      emit(`${type}s_follow_updated`);

      data.follower_count = response.data.follower_count;
      actions.setState({ [field]: data });
    })
    .catch((response) => {
      apiCatch(response);

      // 关注出错后，还原关注状态
      changeFollow();
    });
};

/**
 * users, topics, users_dialog, index_topics 切换关注状态
 * @param type
 * @param id
 * @param state
 * @param actions
 */
const toggleInList = (type, id, state, actions) => {
  const fieldsMap = {
    users: () => [`${state.tabs[state.tabIndex]}_data`, 'user', 'user_id'],
    topics: () => [`${state.tabs[state.tabIndex]}_data`, 'topic', 'topic_id'],
    users_dialog: () => ['data', 'user', 'user_id'],
    index_topics: () => ['topics_data', 'topic', 'topic_id'],
  };

  const [dataField, field, fieldId] = fieldsMap[type]();

  const data = state[dataField];

  data.forEach((item, index) => {
    if (item[fieldId] !== id) {
      return;
    }

    const changeFollow = () => {
      data[index].relationships.is_following = !item.relationships.is_following;
      actions.setState({ [dataField]: data });
    };

    changeFollow();

    const toggleFollow = () => {
      const func = item.relationships.is_following
        ? getAddFollowFunction(type)
        : getDeleteFollowFunction(type);

      return func({ [fieldId]: id });
    };

    toggleFollow()
      .then(() => {
        emit(`${field}s_follow_updated`);
      })
      .catch((response) => {
        apiCatch(response);

        changeFollow();
      });
  });
};

/**
 * 在 user-popover 中切换对用户的关注状态，用于列表
 * @param primaryKey 提问ID或文章ID或评论ID的字段名
 * @param id 提问ID或文章ID或评论ID
 * @param dataName 数据列表的参数名
 * @param actions
 */
const toggleUserInRelationshipsInList = (primaryKey, id, dataName, actions) => {
  let state;

  // 评论列表
  if (dataName === 'comments_data') {
    state = window.app.comments.getState();
    actions = window.app.comments;
  } else {
    state = actions.getState();
  }

  const data = state[dataName];

  data.forEach((item, index) => {
    if (item[primaryKey] !== id) {
      return;
    }

    const changeFollow = () => {
      const isFollowing = item.relationships.user.relationships.is_following;

      data[index].relationships.user.relationships.is_following = !isFollowing;

      actions.setState({ [dataName]: data });
    };

    changeFollow();

    const toggleFollow = () => {
      const func = item.relationships.user.relationships.is_following
        ? addFollowUser
        : deleteFollowUser;

      return func({ user_id: item.user_id });
    };

    toggleFollow()
      .then(() => {
        emit('users_follow_updated');
      })
      .catch((response) => {
        apiCatch(response);

        changeFollow();
      });
  });
};

/**
 * 在 user-popover 中切换对用户的关注状态，用于文章和提问详情
 * @param primaryKey
 * @param dataName
 * @param actions
 */
const toggleUserInRelationships = (primaryKey, dataName, actions) => {
  const state = actions.getState();
  const data = state[dataName];

  const changeFollow = () => {
    data.relationships.user.relationships.is_following = !data.relationships
      .user.relationships.is_following;
    actions.setState({ [dataName]: data });
  };

  changeFollow();

  const func = data.relationships.user.relationships.is_following
    ? addFollowUser
    : deleteFollowUser;

  func({ user_id: data.user_id })
    .then(() => {
      emit('users_follow_updated');
    })
    .catch((response) => {
      apiCatch(response);

      changeFollow();
    });
};

/**
 * 在需要用到关注的页面中，引入该 actions
 */
export default {
  /**
   * @param type article, question, user, users, topic, topics, users_dialog, index_topics, relationships-user
   * @param dataName 仅 relationships-user 中需要传入该参数
   * @param primaryKey 仅 relationships-user 需要传入该参数，表示提问ID或文章ID或评论ID的字段名
   * @param id 仅 users, topics, relationships-user 中需要传入该参数
   */
  toggleFollow: ({ type, dataName = null, primaryKey = null, id = null }) => (
    state,
    actions,
  ) => {
    if (!currentUser()) {
      emit('login_open');
      return;
    }

    // 文章、提问、用户、话题详情页
    if (['article', 'question', 'user', 'topic'].indexOf(type) > -1) {
      toggleOne(type, state, actions);
      return;
    }

    // 用户、话题列表页，及 users_dialog 组件中
    if (
      ['users', 'topics', 'index_topics', 'users_dialog'].indexOf(type) > -1
    ) {
      toggleInList(type, id, state, actions);
      return;
    }

    // 在 user-popover 中，且对用户的关注状态
    if (['relationships-user'].indexOf(type) > -1) {
      if (dataName === 'article' || dataName === 'question') {
        toggleUserInRelationships(primaryKey, dataName, actions);
      } else {
        toggleUserInRelationshipsInList(primaryKey, id, dataName, actions);
      }
    }
  },
};
