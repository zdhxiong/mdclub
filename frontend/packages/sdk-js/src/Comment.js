import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取指定用户发表的评论
   *
   * GET /users/{user_id}/comments
   */
  getListByUserId(user_id, data, success) {
    get(`/users/${user_id}/comments`, data, success);
  },

  /**
   * 获取当前用户发表的评论
   *
   * GET /user/comments
   */
  getMyList(data, success) {
    get('/user/comments', data, success);
  },

  /**
   * 获取评论列表
   *
   * GET /comments
   */
  getList(data, success) {
    get('/comments', data, success);
  },

  /**
   * 删除多个评论
   *
   * DELETE /comments
   */
  deleteMultiple(comment_id, success) {
    del('/comments', { comment_id }, success);
  },

  /**
   * 获取指定评论
   *
   * GET /comments/{comment_id}
   */
  getOne(comment_id, success) {
    get(`/comments/${comment_id}`, success);
  },

  /**
   * 修改指定评论
   *
   * PATCH /comments/{comment_id}
   */
  updateOne(comment_id, data, success) {
    patch(`/comments/${comment_id}`, data, success);
  },

  /**
   * 删除指定评论
   *
   * DELETE /comments/{comment_id}
   */
  deleteOne(comment_id, success) {
    del(`/comments/${comment_id}`, success);
  },

  /**
   * 获取指定评论的投票者
   *
   * GET /comments/{comment_id}/voters
   */
  getVoters(comment_id, data, success) {
    get(`/comments/${comment_id}/voters`, data, success);
  },

  /**
   * 添加投票
   *
   * POST /comments/{comment_id}/voters
   */
  addVote(comment_id, data, success) {
    post(`/comments/${comment_id}/voters`, data, success);
  },

  /**
   * 取消投票
   *
   * DELETE /comments/{comment_id}/voters
   */
  deleteVote(comment_id, success) {
    del(`/comments/${comment_id}/voters`, success);
  },

  /**
   * 获取已删除的评论列表
   *
   * GET /trash/comments
   */
  getDeletedList(data, success) {
    get('/trash/comments', data, success);
  },

  /**
   * 恢复多个评论
   *
   * POST /trash/comments
   */
  restoreMultiple(comment_id, success) {
    post('/trash/comments', { comment_id }, success);
  },

  /**
   * 销毁已删除的多个评论
   *
   * DELETE /trash/comments
   */
  destroyMultiple(comment_id, success) {
    del('/trash/comments', { comment_id }, success);
  },

  /**
   * 恢复指定评论
   *
   * POST /trash/comments/{comment_id}
   */
  restoreOne(comment_id, success) {
    post(`/trash/comments/${comment_id}`, success);
  },

  /**
   * 销毁指定的已删除的评论
   *
   * DELETE /trash/comments/{comment_id}
   */
  destroyOne(comment_id, success) {
    del(`/trash/comments/${comment_id}`, success);
  },
};
