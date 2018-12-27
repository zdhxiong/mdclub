import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {

  /**
   * 获取指定用户发表的提问列表
   *
   * GET /users/{user_id}/questions
   */
  getListByUserId(user_id, data, success) {
    get(`/users/${user_id}/questions`, data, success);
  },

  /**
   * 获取当前用户发表的提问列表
   *
   * GET /user/questions
   */
  getMyList(data, success) {
    get('/user/questions', data, success);
  },

  /**
   * 获取指定话题下的提问列表
   *
   * GET /topics/{topic_id}/questions
   */
  getListByTopicId(topic_id, data, success) {
    get(`/topics/${topic_id}/questions`, data, success);
  },

  /**
   * 获取提问列表
   *
   * GET /questions
   */
  getList(data, success) {
    get('/questions', data, success);
  },

  /**
   * 创建提问
   *
   * POST /questions
   */
  create(data, success) {
    post('/questions', data, success);
  },

  /**
   * 删除多个提问
   *
   * DELETE /questions
   */
  deleteMultiple(question_id, success) {
    del('/questions', { question_id }, success);
  },

  /**
   * 获取指定提问信息
   *
   * GET /questions/{question_id}
   */
  getOne(question_id, success) {
    get(`/questions/${question_id}`, success);
  },

  /**
   * 更新指定提问
   *
   * PATCH /questions/{question_id}
   */
  updateOne(question_id, data, success) {
    patch(`/questions/${question_id}`, data, success);
  },

  /**
   * 删除指定提问
   *
   * DELETE /questions/{question_id}
   */
  deleteOne(question_id, success) {
    del(`/questions/${question_id}`, success);
  },

  /**
   * 获取评论列表
   *
   * GET /questions/{question_id}/comments
   */
  getComments(question_id, data, success) {
    get(`/questions/${question_id}/comments`, data, success);
  },

  /**
   * 发表评论
   *
   * POST /questions/{question_id}/comments
   */
  addComment(question_id, data, success) {
    post(`/questions/${question_id}/comments`, data, success);
  },

  /**
   * 获取投票者列表
   *
   * GET /questions/{question_id}/voters
   */
  getVoters(question_id, data, success) {
    get(`/questions/${question_id}/voters`, data, success);
  },

  /**
   * 添加投票
   *
   * POST /questions/{question_id}/voters
   */
  addVote(question_id, data, success) {
    post(`/questions/${question_id}/voters`, data, success);
  },

  /**
   * 取消投票
   *
   * DELETE /questions/{question_id}/voters
   */
  deleteVote(question_id, success) {
    del(`/questions/${question_id}/voters`, success);
  },

  /**
   * 获取指定用户关注的提问列表
   *
   * GET /users/{user_id}/following_questions
   */
  getFollowing(user_id, data, success) {
    get(`/users/${user_id}/following_questions`, data, success);
  },

  /**
   * 获取登录用户关注的提问
   *
   * GET /user/following_questions
   */
  getMyFollowing(data, success) {
    get('/user/following_questions', data, success);
  },

  /**
   * 获取指定提问的关注者
   *
   * GET /questions/{question_id}/followers
   */
  getFollowers(question_id, data, success) {
    get(`/questions/${question_id}/followers`, data, success);
  },

  /**
   * 添加关注
   *
   * POST /questions/{question_id}/followers
   */
  addFollow(question_id, success) {
    post(`/questions/${question_id}/followers`, success);
  },

  /**
   * 取消关注
   *
   * DELETE /questions/{question_id}/followers
   */
  deleteFollow(question_id, success) {
    del(`/questions/${question_id}/followers`, success);
  },

  /**
   * 获取已删除的提问列表
   *
   * GET /trash/questions
   */
  getDeletedList(data, success) {
    get('/trash/questions', data, success);
  },

  /**
   * 恢复多个提问
   *
   * POST /trash/questions
   */
  restoreMultiple(question_id, success) {
    post('/trash/questions', { question_id }, success);
  },

  /**
   * 销毁已删除的多个提问
   *
   * DELETE /trash/questions
   */
  destroyMultiple(question_id, success) {
    del('/trash/questions', { question_id }, success);
  },

  /**
   * 恢复指定提问
   *
   * POST /trash/questions/{question_id}
   */
  restoreOne(question_id, success) {
    post(`/trash/questions/${question_id}`, success);
  },

  /**
   * 销毁指定的已删除的提问
   *
   * DELETE /trash/questions/{question_id}
   */
  destroyOne(question_id, success) {
    del(`/trash/questions/${question_id}`, success);
  },
};
