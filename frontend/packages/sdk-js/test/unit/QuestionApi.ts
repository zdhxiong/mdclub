import * as QuestionApi from '../../es/QuestionApi';
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

const questionCreateData: any = {
  title: '提问1',
  topic_id: [1, 2],
  content_markdown: 'test',
  include: ['user', 'topics', 'is_following', 'voting'],
};

const questionUpdateData: any = {
  question_id: 1,
  title: '更新后的提问',
  topic_id: [1],
  content_rendered: '<p>new</p>',
  include: ['user', 'topics', 'is_following', 'voting'],
};

// 仅管理员可获取的字段
const privateFields = ['delete_time'];

describe('QuestionApi', () => {
  it('create - 未登录', () =>
    needLogin(QuestionApi.create, questionCreateData));

  it('create - 已登录，验证失败', () => {
    setDefaultTokenToNormal();

    return QuestionApi.create({
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

    return QuestionApi.create(questionCreateData).then(response => {
      matchModel(response.data, models.Question);
      deepEqual(response.data.title, questionCreateData.title);
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

    return QuestionApi.get({
      question_id: 99999,
    })
      .then(() => failed())
      .catch(response =>
        successWhen(response.code === errors.QUESTION_NOT_FOUND),
      );
  });

  it('get - 未登录', () => {
    removeDefaultToken();

    return QuestionApi.get({
      question_id: 1,
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Question);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 普通用户', () => {
    setDefaultTokenToNormal();

    return QuestionApi.get({
      question_id: 1,
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Question);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 管理员', () => {
    setDefaultTokenToManager();

    return QuestionApi.get({
      question_id: 1,
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      matchModel(response.data, models.Question);
      include(response.data, privateFields);
    });
  });

  it('getList', () => {
    removeDefaultToken();

    return QuestionApi.getList({
      include: ['user', 'topics', 'is_following', 'voting'],
    }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Question);
    });
  });

  it('update - 未登录', () =>
    needLogin(QuestionApi.update, questionUpdateData));
  it('update - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.update(questionUpdateData).then(response => {
      matchModel(response.data, models.Question);
      deepEqual(response.data.title, questionUpdateData.title);
      deepEqual(
        response.data.relationships!.topics!.map(topic => topic.topic_id),
        [1],
      );
      deepEqual(response.data.content_markdown, 'new');
      deepEqual(
        response.data.content_rendered,
        questionUpdateData.content_rendered,
      );
    });
  });

  it('addFollow - 未登录', () =>
    needLogin(QuestionApi.addFollow, { question_id: 1 }));
  it('addFollow - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.addFollow({ question_id: 1 }).then(response =>
      deepEqual(response.data.follower_count, 1),
    );
  });

  it('getFollowers - 未登录', () => {
    removeDefaultToken();

    return QuestionApi.getFollowers({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getFollowers - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.getFollowers({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('deleteFollow - 未登录', () =>
    needLogin(QuestionApi.deleteFollow, { question_id: 1 }));
  it('deleteFollow - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.deleteFollow({ question_id: 1 }).then(response =>
      deepEqual(response.data.follower_count, 0),
    );
  });

  it('addVote - 未登录', () =>
    needLogin(QuestionApi.addVote, { question_id: 1, type: 'up' }));
  it('addVote - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.addVote({ question_id: 1, type: 'up' })
      .then(response => deepEqual(response.data.vote_count, 1))
      .then(() => QuestionApi.addVote({ question_id: 1, type: 'down' }))
      .then(response => deepEqual(response.data.vote_count, -1));
  });

  it('getVoters - 未登录', () => {
    removeDefaultToken();

    return QuestionApi.getVoters({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });
  it('getVoters - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.getVoters({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.User);
    });
  });

  it('deleteVote - 未登录', () =>
    needLogin(QuestionApi.deleteVote, { question_id: 1 }));
  it('deleteVote - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.deleteVote({ question_id: 1 }).then(response =>
      deepEqual(response.data.vote_count, 0),
    );
  });

  it('createAnswer - 未登录', () =>
    needLogin(QuestionApi.createAnswer, {
      question_id: 1,
      content_markdown: 'test',
    }));
  it('createAnswer - 已登录，question 不存在', () => {
    setDefaultTokenToNormal();

    return QuestionApi.createAnswer({
      question_id: 99999,
      content_markdown: 'test',
    })
      .then(() => failed())
      .catch(response =>
        successWhen(response.code === errors.QUESTION_NOT_FOUND),
      );
  });
  it('createAnswer - 已登录，验证失败', () => {
    setDefaultTokenToNormal();

    return QuestionApi.createAnswer({
      question_id: 1,
      content_markdown: '',
    })
      .then(() => failed())
      .catch(response => {
        successWhen(response.code === errors.COMMON_FIELD_VERIFY_FAILED);
        deepEqual(Object.keys(response.errors), [
          'content_markdown',
          'content_rendered',
        ]);
      });
  });
  it('createAnswer - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.createAnswer({
      question_id: 1,
      content_markdown: 'test',
    }).then(response => {
      matchModel(response.data, models.Answer);
      deepEqual(response.data.content_markdown, 'test');
      deepEqual(response.data.content_rendered, '<p>test</p>');
    });
  });

  it('getAnswers - 未登录', () => {
    removeDefaultToken();

    return QuestionApi.getAnswers({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Answer);
    });
  });
  it('getAnswers - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.getAnswers({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Answer);
    });
  });

  it('createComment - 未登录', () =>
    needLogin(QuestionApi.createComment, { question_id: 1, content: 'test' }));
  it('createComment - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.createComment({ question_id: 1, content: 'test' }).then(
      response => {
        matchModel(response.data, models.Comment);
      },
    );
  });

  it('getComments - 未登录', () => {
    removeDefaultToken();

    return QuestionApi.getComments({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Comment);
    });
  });
  it('getComments - 已登录', () => {
    setDefaultTokenToNormal();

    return QuestionApi.getComments({ question_id: 1 }).then(response => {
      lengthOf(response.data, 1);
      matchModel(response.data[0], models.Comment);
    });
  });

  it('get - 发布回答和评论后检查提问中的数量', () => {
    removeDefaultToken();

    return QuestionApi.get({ question_id: 1 }).then(response => {
      matchModel(response.data, models.Question);
      deepEqual(response.data.answer_count, 1);
      deepEqual(response.data.comment_count, 1);
    });
  });
});
