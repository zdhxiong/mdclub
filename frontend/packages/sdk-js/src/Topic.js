import {
  get,
  post,
  put,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取话题列表
   *
   * GET /topics
   */
  getList(data, success) {
    get('/topics', data, success);
  },

  /**
   * 发布话题
   *
   * POST /topics
   */
  create(name, description, file, success) {
    const data = new FormData();
    data.append('name', name);
    data.append('description', description);
    data.append('file', file);

    post('/topics', data, success);
  },

  /**
   * 删除多个话题
   *
   * DELETE /topics
   */
  deleteMultiple(topic_id, success) {
    del('/topics', { topic_id }, success);
  },

  /**
   * 获取指定话题信息
   *
   * GET /topics/{topic_id}
   */
  getOne(topic_id, success) {
    get(`/topics/${topic_id}`, success);
  },

  /**
   * 更新话题信息
   *
   * POST /topics/{topic_id}
   */
  updateOne(topic_id, params, success) {
    const data = new FormData();

    if (typeof params.name !== 'undefined') {
      data.append('name', params.name);
    }

    if (typeof params.description !== 'undefined') {
      data.append('description', params.description);
    }

    if (typeof params.file !== 'undefined') {
      data.append('file', params.file);
    }

    post(`/topics/${topic_id}`, data, success);
  },

  /**
   * 删除指定话题
   *
   * DELETE /topics/{topic_id}
   */
  deleteOne(topic_id, success) {
    del(`/topics/${topic_id}`, success);
  },

  /**
   * 获取指定用户关注的话题列表
   *
   * GET /users/{user_id}/following_topics
   */
  getFollowing(user_id, data, success) {
    get(`/users/${user_id}/following_topics`, data, success);
  },

  /**
   * 获取当前用户关注的话题列表
   *
   * GET /user/following_topics
   */
  getMyFollowing(data, success) {
    get('/user/following_topics', data, success);
  },

  /**
   * 获取指定话题的关注者
   *
   * GET /topics/{topic_id}/followers
   */
  getFollowers(topic_id, data, success) {
    get(`/topics/${topic_id}/followers`, data, success);
  },

  /**
   * 添加关注
   *
   * POST /topics/{topic_id}/followers
   */
  addFollow(topic_id, success) {
    post(`/topics/${topic_id}/followers`, success);
  },

  /**
   * 取消关注
   *
   * DELETE /topics/{topic_id}/followers
   */
  deleteFollow(topic_id, success) {
    del(`/topics/${topic_id}/followers`, success);
  },

  /**
   * 获取已删除的话题列表
   *
   * GET /trash/topics
   */
  getDeletedList(data, success) {
    get('/trash/topics', data, success);
  },

  /**
   * 恢复多个话题
   *
   * POST /trash/topics
   */
  restoreMultiple(topic_id, success) {
    post('/trash/topics', { topic_id }, success);
  },

  /**
   * 销毁已删除的多个话题
   *
   * DELETE /trash/topics
   */
  destroyMultiple(topic_id, success) {
    del('/trash/topics', { topic_id }, success);
  },

  /**
   * 恢复指定话题
   *
   * POST /trash/topics/{topic_id}
   */
  restoreOne(topic_id, success) {
    post(`/trash/topics/${topic_id}`, success);
  },

  /**
   * 销毁指定的已删除的话题
   *
   * DELETE /trash/topics/{topic_id}
   */
  destroyOne(topic_id, success) {
    del(`/trash/topics/${topic_id}`, success);
  },
};
