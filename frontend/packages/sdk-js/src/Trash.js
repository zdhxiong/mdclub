import {
  get,
  post,
  del,
} from './util/requestAlias';

export default {
  /**
   * 清空回收站
   *
   * DELETE /trash
   */
  deleteAll(success) {
    del('/trash', success);
  },

  /**
   * 获取用户列表
   *
   * GET /trash/users
   */
  getUsers(data, success) {
    get('/trash/users', data, success);
  },

  /**
   * 恢复指定用户
   *
   * POST /trash/users/{user_id}
   */
  restoreUser(user_id, success) {
    post(`/trash/users/${user_id}`, success);
  },

  /**
   * 获取话题列表
   *
   * GET /trash/topics
   */
  getTopics(data, success) {
    get('/trash/topics', data, success);
  },

  /**
   * 恢复指定话题
   *
   * POST /trash/topics/{topic_id}
   */
  restoreTopic(topic_id, success) {
    post(`/trash/topics/${topic_id}`, success);
  },

  /**
   * 删除指定话题
   *
   * DELETE /trash/topics/{topic_id}
   */
  deleteTopic(topic_id, success) {
    del(`/trash/topics/${topic_id}`, success);
  },

  /**
   * 获取问题列表
   *
   * GET /trash/questions
   */
  getQuestions(data, success) {
    get('/trash/questions', data, success);
  },

  /**
   * 恢复指定问题
   *
   * POST /trash/questions/{question_id}
   */
  restoreQuestion(question_id, success) {
    post(`/trash/questions/${question_id}`, success);
  },

  /**
   * 删除指定问题
   *
   * DELETE /trash/questions/{question_id}
   */
  deleteQuestion(question_id, success) {
    del(`/trash/questions/${question_id}`, success);
  },

  /**
   * 获取回答列表
   *
   * GET /trash/answers
   */
  getAnswers(data, success) {
    get('/trash/answers', data, success);
  },

  /**
   * 恢复指定回答
   *
   * POST /trash/answers/{answer_id}
   */
  restoreAnswer(answer_id, success) {
    post(`/trash/answers/${answer_id}`, success);
  },

  /**
   * 删除指定问题
   *
   * DELETE /trash/answers/{answer_id}
   */
  deleteAnswer(answer_id, success) {
    del(`/trash/answers/${answer_id}`, success);
  },

  /**
   * 获取文章列表
   *
   * GET /trash/articles
   */
  getArticles(data, success) {
    get('/trash/articles', data, success);
  },

  /**
   * 恢复指定文章
   *
   * POST /trash/articles/{article_id}
   */
  restoreArticle(article_id, success) {
    post(`/trash/articles/${article_id}`, success);
  },

  /**
   * 删除指定文章
   *
   * DELETE /trash/articles/{article_id}
   */
  deleteArticle(article_id, success) {
    del(`/trash/articles/${article_id}`, success);
  },

  /**
   * 获取评论列表
   *
   * GET /trash/comments
   */
  getComments(data, success) {
    get('/trash/comments', data, success);
  },

  /**
   * 恢复指定评论
   *
   * POST /trash/comments/{comment_id}
   */
  restoreComment(comment_id, success) {
    post(`/trash/comments/${comment_id}`, success);
  },

  /**
   * 删除指定评论
   *
   * DELETE /trash/comments/{comment_id}
   */
  deleteComment(comment_id, success) {
    del(`/trash/comments/${comment_id}`, success);
  },
};
