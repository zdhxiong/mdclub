import * as ArticleApi from '../../es/ArticleApi';
import errors from '../utils/errors';
import models from '../utils/models';
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
  notInclude,
} from '../utils/validator';
import { failed, successWhen } from '../utils/result';

const articleCreateData: any = {
  title: '文章1',
  topic_id: [1, 2],
  content_markdown: 'test',
  include: ['user', 'topics', 'is_following', 'voting'],
};

const articleUpdateData: any = {
  article_id: 1,
  title: '更新后的提问',
  topic_id: [1],
  content_rendered: '<p>new</p>',
  include: ['user', 'topics', 'is_following', 'voting'],
};

// 仅管理员可获取的字段
const privateFields = ['delete_time'];

describe('ArticleApi', () => {
  it('create - 未登录', () => needLogin(ArticleApi.create, articleCreateData));

  it('create - 已登录，验证失败', () => {
    setDefaultTokenToNormal();

    return ArticleApi.create({
      title: 'title',
      topic_id: [2, 5],
      content_markdown: 'test',
    })
      .then(() => failed())
      .catch(response => {
        successWhen(response.code === errors.COMMON_FIELD_VERIFY_FAILED);
        deepEqual(Object.keys(response.errors), ['topic_id']);
      });
  });

  it('create - 已登录，发布成功', () => {
    setDefaultTokenToNormal();

    return ArticleApi.create(articleCreateData).then(response => {
      matchModel(response.data, models.Article);
      deepEqual(response.data.title, articleCreateData.title);
      deepEqual(
        response.data.relationships!.topics!.map(topic => topic.topic_id),
        [1, 2],
      );
      deepEqual(response.data.content_markdown, 'test');
      deepEqual(response.data.content_rendered, '<p>test</p>');
    });
  });

  it('get - 不存在', () => {
    removeDefaultToken();

    return ArticleApi.get({
      article_id: 99999,
    })
      .then(() => failed())
      .catch(response =>
        successWhen(response.code === errors.ARTICLE_NOT_FOUND),
      );
  });

  it('get - 未登录', () => {
    removeDefaultToken();

    return ArticleApi.get({
      article_id: 1,
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Article);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 普通用户', () => {
    setDefaultTokenToNormal();

    return ArticleApi.get({
      article_id: 1,
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Article);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 管理与', () => {
    setDefaultTokenToManager();

    return ArticleApi.get({
      article_id: 1,
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Article);
      include(response.data, privateFields);
    });
  });

  it('getList', function() {
    removeDefaultToken();

    return ArticleApi.getList({
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Article);
    });
  });

  it('update - 未登录', () => needLogin(ArticleApi.update, articleUpdateData));
  it('update - 已登录', function() {
    setDefaultTokenToNormal();

    return ArticleApi.update(articleUpdateData).then(response => {
      matchModel(response.data, models.Article);
      deepEqual(response.data.title, articleUpdateData.title);
      deepEqual(
        response.data.relationships!.topics!.map(topic => topic.topic_id),
        [1],
      );
      deepEqual(response.data.content_markdown, 'new');
      deepEqual(
        response.data.content_rendered,
        articleUpdateData.content_rendered,
      );
    });
  });

  it('addFollow - 未登录', () =>
    needLogin(ArticleApi.addFollow, { article_id: 1 }));
  it('addFollow - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.addFollow({ article_id: 1 }).then(response =>
      deepEqual(response.data.follower_count, 1),
    );
  });

  it('getFollowers - 未登录', () => {
    removeDefaultToken();

    return ArticleApi.getFollowers({ article_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getFollowers - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.getFollowers({ article_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('deleteFollow - 未登录', () =>
    needLogin(ArticleApi.deleteFollow, { article_id: 1 }));
  it('deleteFollow - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.deleteFollow({ article_id: 1 }).then(response =>
      deepEqual(response.data.follower_count, 0),
    );
  });

  it('addVote - 未登录', () =>
    needLogin(ArticleApi.addVote, { article_id: 1, type: 'up' }));
  it('addVote - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.addVote({ article_id: 1, type: 'up' })
      .then(response => deepEqual(response.data.vote_count, 1))
      .then(() => ArticleApi.addVote({ article_id: 1, type: 'down' }))
      .then(response => deepEqual(response.data.vote_count, -1));
  });

  it('getVoters - 未登录', () => {
    removeDefaultToken();

    return ArticleApi.getVoters({ article_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getVoters - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.getVoters({ article_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('deleteVote - 未登录', () =>
    needLogin(ArticleApi.deleteVote, { article_id: 1 }));
  it('deleteVote - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.deleteVote({ article_id: 1 }).then(response =>
      deepEqual(response.data.vote_count, 0),
    );
  });

  it('createComment - 未登录', () =>
    needLogin(ArticleApi.createComment, { article_id: 1, content: 'test' }));
  it('createComment - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.createComment({ article_id: 1, content: 'test' }).then(
      response => {
        matchModel(response.data, models.Comment);
      },
    );
  });

  it('getComments - 未登录', () => {
    removeDefaultToken();

    return ArticleApi.getComments({ article_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Comment);
    });
  });
  it('getComments - 已登录', () => {
    setDefaultTokenToNormal();

    return ArticleApi.getComments({ article_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Comment);
    });
  });

  it('get - 发布评论后检查文章中的数量', () => {
    removeDefaultToken();

    return ArticleApi.get({ article_id: 1 }).then(response => {
      matchModel(response.data, models.Article);
      deepEqual(response.data.comment_count, 1);
    });
  });
});
