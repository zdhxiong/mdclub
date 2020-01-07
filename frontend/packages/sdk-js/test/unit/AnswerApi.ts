import * as AnswerApi from '../../es/AnswerApi';
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

const answerUpdateData: any = {
  answer_id: 1,
  content_rendered: '<p>new</p>',
};

// 仅管理员可获取的字段
const privateFields = ['delete_time'];

describe('AnswerApi', () => {
  it('get - 不存在', () => {
    removeDefaultToken();

    return AnswerApi.get({ answer_id: 99999 })
      .then(() => failed())
      .catch(response =>
        successWhen(response.code === errors.ANSWER_NOT_FOUND),
      );
  });

  it('get - 未登录', () => {
    removeDefaultToken();

    return AnswerApi.get({
      answer_id: 1,
      include: ['user', 'voting', 'question'],
    }).then(response => {
      matchModel(response.data, models.Answer);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 普通用户', () => {
    setDefaultTokenToNormal();

    return AnswerApi.get({
      answer_id: 1,
      include: ['user', 'voting', 'question'],
    }).then(response => {
      matchModel(response.data, models.Answer);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 管理员', () => {
    setDefaultTokenToManager();

    return AnswerApi.get({
      answer_id: 1,
      include: ['user', 'voting', 'question'],
    }).then(response => {
      matchModel(response.data, models.Answer);
      include(response.data, privateFields);
    });
  });

  it('getList - 未登录', () => needLogin(AnswerApi.getList));
  it('getList - 普通用户', () => needManager(AnswerApi.getList));
  it('getList - 管理员', () => {
    setDefaultTokenToManager();

    return AnswerApi.getList().then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Answer);
    });
  });

  it('update - 未登录', () => needLogin(AnswerApi.update, answerUpdateData));
  it('update - 已登录', () => {
    setDefaultTokenToNormal();

    return AnswerApi.update(answerUpdateData).then(response => {
      matchModel(response.data, models.Answer);
      deepEqual(response.data.answer_id, answerUpdateData.answer_id);
      deepEqual(response.data.content_markdown, 'new');
      deepEqual(
        response.data.content_rendered,
        answerUpdateData.content_rendered,
      );
    });
  });

  it('addVote - 未登录', () =>
    needLogin(AnswerApi.addVote, { answer_id: 1, type: 'up' }));
  it('addVote - 已登录', () => {
    setDefaultTokenToNormal();

    return AnswerApi.addVote({ answer_id: 1, type: 'up' })
      .then(response => deepEqual(response.data.vote_count, 1))
      .then(() => AnswerApi.addVote({ answer_id: 1, type: 'down' }))
      .then(response => deepEqual(response.data.vote_count, -1));
  });

  it('getVoters - 未登录', () => {
    removeDefaultToken();

    return AnswerApi.getVoters({ answer_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getVoters - 已登录', () => {
    setDefaultTokenToNormal();

    return AnswerApi.getVoters({ answer_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('deleteVote - 未登录', () =>
    needLogin(AnswerApi.deleteVote, { answer_id: 1 }));
  it('deleteVote - 已登录', () => {
    setDefaultTokenToNormal();

    return AnswerApi.deleteVote({ answer_id: 1 }).then(response =>
      deepEqual(response.data.vote_count, 0),
    );
  });

  it('createComment - 未登录', () =>
    needLogin(AnswerApi.createComment, { answer_id: 1, content: 'test' }));
  it('createComment - 已登录', () => {
    setDefaultTokenToNormal();

    return AnswerApi.createComment({
      answer_id: 1,
      content: 'test',
    }).then(response => matchModel(response.data, models.Comment));
  });

  it('getComments - 未登录', () => {
    removeDefaultToken();

    return AnswerApi.getComments({ answer_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Comment);
    });
  });
  it('getComments - 已登录', () => {
    setDefaultTokenToNormal();

    return AnswerApi.getComments({ answer_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Comment);
    });
  });

  it('get - 发布评论后检查提问中的数量', () => {
    removeDefaultToken();

    return AnswerApi.get({ answer_id: 1 }).then(response => {
      matchModel(response.data, models.Answer);
      deepEqual(response.data.comment_count, 1);
    });
  });
});
