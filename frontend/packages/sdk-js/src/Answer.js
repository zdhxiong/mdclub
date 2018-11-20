import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取指定用户发表的回答
   *
   * GET /users/{user_id}/answers
   */
  getListByUserId(user_id, data, success) {
    get(`/users/${user_id}/answers`, data, success);
  },

  /**
   * 获取我发表的回答列表
   *
   * GET /user/answers
   */
  getMyList(data, success) {
    get('/user/answers', data, success);
  },

  /**
   * 获取指定问题下的回答列表
   *
   * GET /questions/{question_id}/answers
   */
  getListByQuestionId(question_id, data, success) {
    get(`/questions/${question_id}/answers`, data, success);
  },

  /**
   * 创建回答
   *
   * POST /questions/{question_id}/answers
   */
  create(question_id, data, success) {
    post(`/questions/${question_id}/answers`, data, success);
  },

  /**
   * 获取回答列表
   *
   * GET /answers
   */
  getList(data, success) {
    get('/answers', data, success);
  },

  /**
   * 删除多个回答
   *
   * DELETE /answers
   */
  deleteMultiple(answer_id, success) {
    del('/answers', { answer_id }, success);
  },

  /**
   * 获取指定回答信息
   *
   * GET /answers/{answer_id}
   */
  getOne(answer_id, success) {
    get(`/answers/${answer_id}`, success);
  },

  /**
   * 修改指定回答
   *
   * PATCH /answers/{answer_id}
   */
  updateOne(answer_id, data, success) {
    patch(`/answers/${answer_id}`, data, success);
  },

  /**
   * 删除指定回答
   *
   * DELETE /answers/{answer_id}
   */
  deleteOne(answer_id, success) {
    del(`/answers/${answer_id}`, success);
  },

  /**
   * 获取评论列表
   *
   * GET /answers/{answer_id}/comments
   */
  getComments(answer_id, data, success) {
    get(`/answers/${answer_id}/comments`, data, success);
  },

  /**
   * 发表评论
   *
   * POST /answers/{answer_id}/comments
   */
  addComment(answer_id, data, success) {
    post(`/answers/${answer_id}/comments`, data, success);
  },

  /**
   * 获取投票者列表
   *
   * GET /answers/{answer_id}/voters
   */
  getVoters(answer_id, data, success) {
    get(`/answers/${answer_id}/voters`, data, success);
  },

  /**
   * 添加投票
   *
   * POST /answers/{answer_id}/voters
   */
  addVote(answer_id, data, success) {
    post(`/answers/${answer_id}/voters`, data, success);
  },

  /**
   * 取消投票
   *
   * DELETE /answers/{answer_id}/voters
   */
  deleteVote(answer_id, success) {
    del(`/answers/${answer_id}/voters`, success);
  },
};
