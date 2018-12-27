import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取指定用户发表的文章列表
   *
   * GET /users/{user_id}/articles
   */
  getListByUserId(user_id, data, success) {
    get(`/users/${user_id}/articles`, data, success);
  },

  /**
   * 获取我发表的文章列表
   *
   * GET /user/articles
   */
  getMyList(data, success) {
    get('/user/articles', data, success);
  },

  /**
   * 获取指定话题下的文章列表
   *
   * GET /topics/{topic_id}/articles
   */
  getListByTopicId(topic_id, data, success) {
    get(`/topics/${topic_id}/articles`, data, success);
  },

  /**
   * 获取文章列表
   *
   * GET /articles
   */
  getList(data, success) {
    get('/articles', data, success);
  },

  /**
   * 发表文章
   *
   * POST /articles
   */
  create(data, success) {
    post('/articles', data, success);
  },

  /**
   * 删除多个文章
   *
   * DELETE /articles
   */
  deleteMultiple(article_id, success) {
    del('/articles', { article_id }, success);
  },

  /**
   * 获取指定文章信息
   *
   * GET /articles/{article_id}
   */
  getOne(article_id, success) {
    get(`/articles/${article_id}`, success);
  },

  /**
   * 修改指定文章
   *
   * PATCH /articles/{article_id}
   */
  updateOne(article_id, data, success) {
    patch(`/articles/${article_id}`, data, success);
  },

  /**
   * 删除指定文章
   *
   * DELETE /articles/{article_id}
   */
  deleteOne(article_id, success) {
    del(`/articles/${article_id}`, success);
  },

  /**
   * 获取评论列表
   *
   * GET /articles/{article_id}/comments
   */
  getComments(article_id, data, success) {
    get(`/articles/${article_id}/comments`, data, success);
  },

  /**
   * 发表评论
   *
   * POST /articles/{article_id}/comments
   */
  addComment(article_id, data, success) {
    post(`/articles/${article_id}/comments`, data, success);
  },

  /**
   * 获取投票者列表
   *
   * GET /articles/{article_id}/voters
   */
  getVoters(article_id, data, success) {
    get(`/articles/${article_id}/voters`, data, success);
  },

  /**
   * 添加投票
   *
   * POST /articles/{article_id}/voters
   */
  addVote(article_id, data, success) {
    post(`/articles/${article_id}/voters`, data, success);
  },

  /**
   * 取消投票
   *
   * DELETE /articles/{article_id}/voters
   */
  deleteVote(article_id, success) {
    del(`/articles/${article_id}/voters`, success);
  },

  /**
   * 获取指定用户关注的文章列表
   *
   * GET /users/{user_id}/following_articles
   */
  getFollowing(user_id, data, success) {
    get(`/users/${user_id}/following_articles`, data, success);
  },

  /**
   * 获取当前用户关注的文章列表
   *
   * GET /user/following_articles
   */
  getMyFollowing(data, success) {
    get('/user/following_articles', data, success);
  },

  /**
   * 获取指定文章的关注者
   *
   * GET /articles/{article_id}/followers
   */
  getFollowers(article_id, data, success) {
    get(`/articles/${article_id}/followers`, data, success);
  },

  /**
   * 添加关注
   *
   * POST /articles/{article_id}/followers
   */
  addFollow(article_id, success) {
    post(`/articles/${article_id}/followers`, success);
  },

  /**
   * 取消关注
   *
   * DELETE /articles/{article_id}/followers
   */
  deleteFollow(article_id, success) {
    del(`/articles/${article_id}/followers`, success);
  },

  /**
   * 获取已删除的文章列表
   *
   * GET /trash/articles
   */
  getDeletedList(data, success) {
    get('/trash/articles', data, success);
  },

  /**
   * 恢复多个文章
   *
   * POST /trash/articles
   */
  restoreMultiple(article_id, success) {
    post('/trash/articles', { article_id }, success);
  },

  /**
   * 销毁已删除的多个文章
   *
   * DELETE /trash/articles
   */
  destroyMultiple(article_id, success) {
    del('/trash/articles', { article_id }, success);
  },

  /**
   * 恢复指定文章
   *
   * POST /trash/articles/{article_id}
   */
  restoreOne(article_id, success) {
    post(`/trash/articles/${article_id}`, success);
  },

  /**
   * 销毁指定的已删除的文章
   *
   * DELETE /trash/articles/{article_id}
   */
  destroyOne(article_id, success) {
    del(`/trash/articles/${article_id}`, success);
  },
};
