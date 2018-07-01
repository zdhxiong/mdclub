import {
  get,
  post,
  put,
  del,
} from './util/requestAlias';

export default {
  /**
   * 最近更新的问题列表
   */
  getRecentList(data, success) {
    get('/questions/recent', data, success);
  },

  /**
   * 获取最近热门问题列表
   */
  getPopularList(data, success) {
    get('/questions/popular', data, success);
  },

  /**
   * 获取问题列表
   */
  getList(data, success) {
    get('/questions', success);
  },

  /**
   * 创建问题
   */
  create(data, success) {
    post('/questions', data, success);
  },

  /**
   * 获取指定问题信息
   */
  getOne(question_id, success) {
    get(`/questions/${question_id}`, success);
  },

  /**
   * 获取指定问题的关注者
   */
  getFollowers(question_id, data, success) {
    get(`/questions/${question_id}/followers`, data, success);
  },

  /**
   * 获取指定用户关注的问题列表
   */
  getFollowing(user_id, data, success) {
    get(`/users/${user_id}/questions/following`, data, success);
  },

  /**
   * 获取登录用户关注的问题
   */
  getMyFollowing(data, success) {
    get('/user/questions/following', data, success);
  },

  /**
   * 检查指定用户是否关注了指定话题
   */
  isFollowing(user_id, question_id, success) {
    get(`/users/${user_id}/questions/${question_id}/following`, success);
  },

  /**
   * 检查当前用户是否关注了指定问题
   */
  isMyFollowing(question_id, success) {
    get(`/user/questions/${question_id}/following`, success);
  },

  /**
   * 添加关注
   */
  addFollow(question_id, success) {
    put(`/user/questions/${question_id}/following`, success);
  },

  /**
   * 取消关注
   */
  deleteFollow(question_id, success) {
    del(`/user/questions/${question_id}/following`, success);
  },
};
