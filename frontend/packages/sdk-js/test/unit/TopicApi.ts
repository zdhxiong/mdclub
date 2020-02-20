import * as TopicApi from '../../es/TopicApi';
import * as errors from '../../es/errors';
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

const topicCreateData: any = {
  name: '测试话题',
  description: '测试话题的描述',
  // 如何生成图片进行测试？
  cover: new Blob(['(⌐□_□)'], { type: 'image/png' }),
};

const topicUpdateData: any = {
  topic_id: 1,
  name: '更新后的测试话题',
  description: '测试后的测试话题的描述',
  // 不测试更新图片
};

// 仅管理员可获取的字段
const privateFields = ['delete_time'];

describe('TopicApi', () => {
  it('create - 未登录', () => needLogin(TopicApi.create, topicCreateData));
  it('create - 已登录', () => needManager(TopicApi.create, topicCreateData));

  // 无法测试上传图片，这里只测试字段验证失败的
  it('create - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.create(topicCreateData)
      .then(() => failed())
      .catch(response => {
        // 未上传图片
        successWhen(response.code === errors.COMMON_FIELD_VERIFY_FAILED);
        deepEqual(Object.keys(response.errors), ['cover']);
      });
  });

  it('get - 不存在', () => {
    removeDefaultToken();

    return TopicApi.get({
      topic_id: 999999,
    })
      .then(() => failed())
      .catch(response => successWhen(response.code === errors.TOPIC_NOT_FOUND));
  });

  it('get - 未登录', () => {
    removeDefaultToken();

    return TopicApi.get({
      topic_id: 1,
      include: ['is_following'],
    }).then(response => {
      matchModel(response.data, models.Topic);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 已登录', () => {
    setDefaultTokenToNormal();

    return TopicApi.get({
      topic_id: 1,
      include: ['is_following'],
    }).then(response => {
      matchModel(response.data, models.Topic);
      notInclude(response.data, privateFields);
    });
  });

  it('get - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.get({
      topic_id: 1,
      include: ['is_following'],
    }).then(response => {
      matchModel(response.data, models.Topic);
      include(response.data, privateFields);
    });
  });

  it('getList - 未登录', () => {
    removeDefaultToken();

    return TopicApi.getList({
      include: ['is_following'],
    }).then(response => {
      lengthOf(response.data, 2);
      matchModel(response.data[0], models.Topic);
      notInclude(response.data[0], privateFields);
    });
  });

  // 未登录添加 trashed 无效
  it('getList - 未登录用户获取回收站中的', () => {
    removeDefaultToken();

    return TopicApi.getList({
      trashed: true,
    }).then(response => {
      lengthOf(response.data, 2);
    });
  });

  it('getList - 已登陆', () => {
    setDefaultTokenToNormal();

    return TopicApi.getList({
      include: ['is_following'],
    }).then(response => {
      lengthOf(response.data, 2);
      matchModel(response.data[0], models.Topic);
      notInclude(response.data[0], privateFields);
    });
  });

  // 普通用户添加 trashed 无效
  it('getList - 普通用户获取回收站中的', () => {
    setDefaultTokenToNormal();

    return TopicApi.getList({
      trashed: true,
    }).then(response => {
      lengthOf(response.data, 2);
    });
  });

  it('getList - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.getList({
      include: ['is_following'],
    }).then(response => {
      lengthOf(response.data, 2);
      matchModel(response.data[0], models.Topic);
      include(response.data[0], privateFields);
    });
  });

  // 管理员可添加 trashed
  it('getList - 管理员获取回收站中的', () => {
    setDefaultTokenToManager();

    return TopicApi.getList({
      trashed: true,
    }).then(response => {
      lengthOf(response.data, 1);
    });
  });

  it('update - 未登录', () => needLogin(TopicApi.update, topicUpdateData));
  it('update - 已登录', () => needManager(TopicApi.update, topicUpdateData));
  it('update - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.update(topicUpdateData).then(response => {
      matchModel(response.data, models.Topic);
      deepEqual(response.data.name, topicUpdateData.name);
      deepEqual(response.data.description, topicUpdateData.description);
    });
  });

  it('update - 回收站中的不能编辑', () => {
    setDefaultTokenToManager();

    return TopicApi.update({
      topic_id: 3,
      name: 'test',
      description: 'test',
    })
      .then(() => failed())
      .catch(response => successWhen(response.code === errors.TOPIC_NOT_FOUND));
  });

  it('trash - 未登录', () => needLogin(TopicApi.trash, { topic_id: 1 }));
  it('trash - 已登录', () => needManager(TopicApi.trash, { topic_id: 1 }));
  it('trash - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.trash({ topic_id: 1 })
      .then(response => matchModel(response.data, models.Topic))
      .then(() => TopicApi.getList({ trashed: true }))
      .then(response => lengthOf(response.data, 2));
  });

  it('untrash - 未登录', () => needLogin(TopicApi.untrash, { topic_id: 1 }));
  it('untrash - 已登录', () => needManager(TopicApi.untrash, { topic_id: 1 }));
  it('untrash - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.untrash({ topic_id: 1 })
      .then(response => matchModel(response.data, models.Topic))
      .then(() => TopicApi.getList({ trashed: true }))
      .then(response => lengthOf(response.data, 1));
  });

  it('trashMultiple - 未登录', () =>
    needLogin(TopicApi.trashMultiple, { topic_ids: '1,2' }));
  it('trashMultiple - 已登录', () =>
    needManager(TopicApi.trashMultiple, { topic_ids: '1,2' }));
  it('trashMultiple - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.trashMultiple({ topic_ids: '1,2' })
      .then(response => {
        lengthOf(response.data, 2);
        matchModel(response.data[0], models.Topic);
      })
      .then(() => TopicApi.getList({ trashed: true }))
      .then(response => lengthOf(response.data, 3));
  });

  it('untrashMultiple - 未登录', () =>
    needLogin(TopicApi.untrashMultiple, { topic_ids: '1,2' }));
  it('untrashMultiple - 已登录', () =>
    needManager(TopicApi.untrashMultiple, { topic_ids: '1,2' }));
  it('untrashMultiple - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.untrashMultiple({ topic_ids: '1,2' })
      .then(response => {
        lengthOf(response.data, 2);
        matchModel(response.data[0], models.Topic);
      })
      .then(() => TopicApi.getList({ trashed: true }))
      .then(response => lengthOf(response.data, 1));
  });

  it('del - 未登录', () => needLogin(TopicApi.del, { topic_id: 1 }));
  it('del - 已登录', () => needManager(TopicApi.del, { topic_id: 1 }));
  it('del - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.del({ topic_id: 1 })
      .then(response => deepEqual(response.data, []))
      .then(() => TopicApi.getList())
      .then(response => lengthOf(response.data, 1));
  });

  it('deleteMultiple - 未登录', () =>
    needLogin(TopicApi.deleteMultiple, { topic_ids: '2,3' }));
  it('deleteMultiple - 已登录', () =>
    needManager(TopicApi.deleteMultiple, { topic_ids: '2,3' }));
  it('deleteMultiple - 管理员', () => {
    setDefaultTokenToManager();

    return TopicApi.deleteMultiple({ topic_ids: '2,3' })
      .then(response => deepEqual(response.data, []))
      .then(() => TopicApi.getList())
      .then(response => lengthOf(response.data, 0))
      .then(() => TopicApi.getList({ trashed: true }))
      .then(response => lengthOf(response.data, 0));
  });
});
