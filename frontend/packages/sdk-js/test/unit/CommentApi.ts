import * as CommentApi from '../../es/CommentApi';
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
  needManager,
  notInclude,
} from '../utils/validator';
import { failed, successWhen } from '../utils/result';

const commentUpdateData: any = {
  comment_id: 1,
  content: '新评论内容',
};

// 仅管理员可获取的字段
const privateFields = ['delete_time'];

describe('CommentApi', () => {
  it('get - 不存在', () => {
    removeDefaultToken();

    return CommentApi.get({ comment_id: 99999 })
      .then(() => failed())
      .catch(response =>
        successWhen(response.code === errors.COMMENT_NOT_FOUND),
      );
  });

  it('get - 未登录', () => {
    removeDefaultToken();

    return CommentApi.get({
      comment_id: 1,
      include: ['user', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Comment);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 普通用户', () => {
    setDefaultTokenToNormal();

    return CommentApi.get({
      comment_id: 1,
      include: ['user', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Comment);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 管理员', () => {
    setDefaultTokenToManager();

    return CommentApi.get({
      comment_id: 1,
      include: ['user', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Comment);
      include(response.data, privateFields);
    });
  });

  it('getList - 未登录', () => needLogin(CommentApi.getList));
  it('getList - 普通用户', () => needManager(CommentApi.getList));
  it('getList - 管理员', () => {
    setDefaultTokenToManager();

    return CommentApi.getList({}).then(response => {
      lengthOf(response.data, 3);
      matchModel(response.data[0], models.Comment);
    });
  });

  it('update - 未登录', () => needLogin(CommentApi.update, commentUpdateData));
  it('update - 已登录', () => {
    setDefaultTokenToNormal();

    return CommentApi.update(commentUpdateData).then(response => {
      matchModel(response.data, models.Comment);
      deepEqual(response.data.comment_id, commentUpdateData.comment_id);
      deepEqual(response.data.content, commentUpdateData.content);
    });
  });

  it('addVote - 未登录', () =>
    needLogin(CommentApi.addVote, { comment_id: 1, type: 'up' }));
  it('addVote - 已登录', () => {
    setDefaultTokenToNormal();

    return CommentApi.addVote({ comment_id: 1, type: 'up' })
      .then(response => deepEqual(response.data.vote_count, 1))
      .then(() => CommentApi.addVote({ comment_id: 1, type: 'down' }))
      .then(response => deepEqual(response.data.vote_count, -1));
  });

  it('getVoters - 未登录', () => {
    removeDefaultToken();

    return CommentApi.getVoters({ comment_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getVoters - 已登录', () => {
    setDefaultTokenToNormal();

    return CommentApi.getVoters({ comment_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('deleteVote - 未登录', () =>
    needLogin(CommentApi.deleteVote, { comment_id: 1 }));
  it('deleteVote - 已登录', () => {
    setDefaultTokenToNormal();

    return CommentApi.deleteVote({ comment_id: 1 }).then(response =>
      deepEqual(response.data.vote_count, 0),
    );
  });
});
