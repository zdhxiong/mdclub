import * as UserApi from '../../es/UserApi';
import * as ArticleApi from '../../es/ArticleApi';
import * as QuestionApi from '../../es/QuestionApi';
import * as TopicApi from '../../es/TopicApi';
import * as errors from '../../es/errors';
import models from '../utils/models';
import extend from 'mdui.jq/es/functions/extend';
import {
  removeDefaultToken,
  setDefaultTokenToManager,
  setDefaultTokenToNormal,
} from '../utils/token';
import {
  deepEqual,
  include,
  lengthOf,
  matchModel,
  needLogin,
  needManager,
  notInclude,
} from '../utils/validator';
import { failed, successWhen } from '../utils/result';

const userUpdateData: any = {
  headline: 'headline-1',
  bio: 'bio-1',
  blog: 'https://mdui.org',
  location: 'china',
};

// 仅管理员可获取的字段
const privateFields = [
  'email',
  'create_ip',
  'create_location',
  'last_login_time',
  'last_login_ip',
  'last_login_location',
  'notification_unread',
  'inbox_unread',
  'update_time',
  'disable_time',
];

describe('UserApi', () => {
  it('getList', () => {
    removeDefaultToken();

    return UserApi.getList({}).then(response => {
      lengthOf(response.data, 2);
      matchModel(response.data[0], models.User);
    });
  });

  it('get - 不存在', () => {
    removeDefaultToken();

    return UserApi.get({
      user_id: 99999,
    })
      .then(() => failed())
      .catch(response => successWhen(response.code === errors.USER_NOT_FOUND));
  });
  it('get - 未登录', () => {
    removeDefaultToken();

    return UserApi.get({
      user_id: 10000,
      include: ['is_followed', 'is_following', 'is_me'],
    }).then(response => {
      matchModel(response.data, models.User);
      notInclude(response.data, privateFields);
    });
  });
  it('get - 普通用户', () => {
    setDefaultTokenToNormal();

    return UserApi.get({
      user_id: 10000,
      include: ['is_followed', 'is_following', 'is_me'],
    }).then(response => {
      matchModel(response.data, models.User);
      notInclude(response.data, privateFields);
    });
  });
  it('get - 管理员', () => {
    setDefaultTokenToManager();

    return UserApi.get({
      user_id: 10000,
      include: ['is_followed', 'is_following', 'is_me'],
    }).then(response => {
      matchModel(response.data, models.User);
      include(response.data, privateFields);
    });
  });

  it('update - 未登录', () => needLogin(UserApi.update, { user_id: 10000 }));
  it('update - 管理员修改别人的信息', () => {
    setDefaultTokenToManager();

    return UserApi.update(
      extend({ user_id: 10000 }, userUpdateData),
    ).then(response => matchModel(response.data, models.User));
  });
  it('update - 普通用户修改别人的信息', () =>
    needManager(UserApi.update, extend({ user_id: 1 }, userUpdateData)));

  it('update - 修改自己的信息', () => {
    setDefaultTokenToNormal();

    return UserApi.updateMine(userUpdateData).then(response => {
      matchModel(response.data, models.User);
    });
  });

  it('updateMine - 未登录', () =>
    needLogin(UserApi.updateMine, userUpdateData));
  it('updateMine - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.updateMine(userUpdateData).then(response => {
      matchModel(response.data, models.User);
    });
  });

  it('deleteMyAvatar - 未登录', () => needLogin(UserApi.deleteMyAvatar, {}));
  it('deleteMyAvatar - 删除自己的头像', () => {
    setDefaultTokenToNormal();

    return UserApi.deleteMyAvatar().then(response => {
      deepEqual(Object.keys(response.data), [
        'original',
        'small',
        'middle',
        'large',
      ]);
    });
  });

  it('deleteMyCover - 未登录', () => needLogin(UserApi.deleteMyCover, {}));
  it('deleteMyCover - 删除自己的封面', () => {
    setDefaultTokenToNormal();

    return UserApi.deleteMyCover().then(response => {
      deepEqual(Object.keys(response.data), [
        'original',
        'small',
        'middle',
        'large',
      ]);
    });
  });

  it('deleteAvatar - 未登录', () =>
    needLogin(UserApi.deleteAvatar, { user_id: 1 }));
  it('deleteAvatar - 普通用户删除别人头像', () =>
    needManager(UserApi.deleteAvatar, { user_id: 1 }));
  it('deleteAvatar - 管理员删除别人头像', () => {
    setDefaultTokenToManager();

    return UserApi.deleteAvatar({ user_id: 10000 }).then(response => {
      deepEqual(Object.keys(response.data), [
        'original',
        'small',
        'middle',
        'large',
      ]);
    });
  });

  it('deleteCover - 未登录', () =>
    needLogin(UserApi.deleteCover, { user_id: 1 }));
  it('deleteCover - 普通用户删除别人封面', () =>
    needManager(UserApi.deleteCover, { user_id: 1 }));
  it('deleteCover - 管理员删除别人封面', () => {
    setDefaultTokenToManager();

    return UserApi.deleteCover({ user_id: 10000 }).then(response => {
      deepEqual(Object.keys(response.data), [
        'original',
        'small',
        'middle',
        'large',
      ]);
    });
  });

  it('addFollow - 未登录', () => needLogin(UserApi.addFollow, { user_id: 1 }));
  it('addFollow - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.addFollow({ user_id: 1 }).then(response =>
      deepEqual(response.data.follower_count, 24),
    );
  });

  it('getFollowers - 未登录', () => {
    removeDefaultToken();

    return UserApi.getFollowers({ user_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getFollowers - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getFollowers({ user_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('getFollowees', () => {
    removeDefaultToken();

    return UserApi.getFollowees({ user_id: 10000 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('getFollowers', () => {
    removeDefaultToken();

    return UserApi.getFollowers({ user_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('getAnswers', () => {
    removeDefaultToken();

    return UserApi.getAnswers({ user_id: 10000 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Answer);
    });
  });

  it('getArticles', () => {
    removeDefaultToken();

    return UserApi.getArticles({ user_id: 10000 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Article);
    });
  });

  it('getComments', () => {
    removeDefaultToken();

    return UserApi.getComments({ user_id: 10000 }).then(response => {
      lengthOf(response.data, 3);
      matchModel(response.data[0], models.Comment);
    });
  });

  it('getFollowingArticles', () => {
    setDefaultTokenToNormal();

    return ArticleApi.addFollow({ article_id: 1 })
      .then(() => UserApi.getFollowingArticles({ user_id: 10000 }))
      .then(response => {
        lengthOf(response.data, 1);
        matchModel(response.data[0], models.Article);
      });
  });

  it('getFollowingQuestions', () => {
    setDefaultTokenToNormal();

    return QuestionApi.addFollow({ question_id: 1 })
      .then(() => UserApi.getFollowingQuestions({ user_id: 10000 }))
      .then(response => {
        lengthOf(response.data, 1);
        matchModel(response.data[0], models.Question);
      });
  });

  it('getFollowingTopics', () => {
    setDefaultTokenToNormal();

    return TopicApi.addFollow({ topic_id: 1 })
      .then(() => UserApi.getFollowingTopics({ user_id: 10000 }))
      .then(response => {
        lengthOf(response.data, 1);
        matchModel(response.data[0], models.Topic);
      });
  });

  it('getMine - 未登录', () => needLogin(UserApi.getMine, {}));
  it('getMine - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMine().then(response => {
      matchModel(response.data, models.User);
    });
  });

  it('getMyAnswers - 未登录', () => needLogin(UserApi.getMyAnswers, {}));
  it('getMyAnswers - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyAnswers().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Answer);
    });
  });

  it('getMyArticles - 未登录', () => needLogin(UserApi.getMyArticles, {}));
  it('getMyArticles - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyArticles().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Article);
    });
  });

  it('getMyComments - 未登录', () => needLogin(UserApi.getMyComments, {}));
  it('getMyComments - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyComments().then(response => {
      lengthOf(response.data, 3);
      matchModel(response.data[0], models.Comment);
    });
  });

  it('getMyFollowees - 未登录', () => needLogin(UserApi.getMyFollowees, {}));
  it('getMyFollowees - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyFollowees().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('getMyFollowers - 未登录', () => needLogin(UserApi.getMyFollowers, {}));
  it('getMyFollowers - 已登录', () => {
    setDefaultTokenToManager();

    return UserApi.getMyFollowers().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('getMyFollowingArticles - 未登录', () =>
    needLogin(UserApi.getMyFollowingArticles, {}));
  it('getMyFollowingArticles - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyFollowingArticles().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Article);
    });
  });

  it('getMyFollowingQuestions - 未登录', () =>
    needLogin(UserApi.getMyFollowingQuestions, {}));
  it('getMyFollowingQuestions - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyFollowingQuestions().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Question);
    });
  });

  it('getMyFollowingTopics - 未登录', () =>
    needLogin(UserApi.getMyFollowingTopics, {}));
  it('getMyFollowingTopics - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyFollowingTopics().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Topic);
    });
  });

  it('getMyQuestions - 未登录', () => needLogin(UserApi.getMyQuestions, {}));
  it('getMyQuestions - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.getMyQuestions().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Question);
    });
  });

  it('getQuestions', () => {
    removeDefaultToken();

    return UserApi.getQuestions({ user_id: 10000 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Question);
    });
  });

  it('deleteFollow - 未登录', () =>
    needLogin(UserApi.deleteFollow, { user_id: 1 }));
  it('deleteFollow - 已登录', () => {
    setDefaultTokenToNormal();

    return UserApi.deleteFollow({ user_id: 1 }).then(response =>
      deepEqual(response.data.follower_count, 23),
    );
  });
});
