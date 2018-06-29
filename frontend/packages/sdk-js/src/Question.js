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
  getRecentList(data, success, error) {
    get('/questions/recent', data, success, error);
  },

  /**
   * 获取最近热门问题列表
   */
  getPopularList(data, success, error) {
    get('/questions/popular', data, success, error);
  },

  /**
   * 获取问题列表
   */
  getList(data, success, error) {
    get('/questions', success, error);
  },

  /**
   * 创建问题
   */
  create(data, success, error) {
    post('/questions', data, success, error);
  },

  /**
   * 获取指定问题信息
   */
  getOne(question_id, success, error) {
    get(`/questions/${question_id}`, success, error);
  },

  /**
   * 获取指定问题的关注者
   */
  getFollowers(question_id, data, success, error) {
    get(`/questions/${question_id}/followers`, data, success, error);
  },

  /**
   * 获取指定用户关注的问题列表
   */
  getFollowing(user_id, data, success, error) {
    get(`/users/${user_id}/questions/following`, data, success, error);
  },

  /**
   * 获取登录用户关注的问题
   */
  getMyFollowing(data, success, error) {
    get('/user/questions/following', data, success, error);
  },

  /**
   * 检查指定用户是否关注了指定话题
   */
  isFollowing(user_id, question_id, success, error) {
    get(`/users/${user_id}/questions/${question_id}/following`, success, error);
  },

  /**
   * 检查当前用户是否关注了指定问题
   */
  isMyFollowing(question_id, success, error) {
    get(`/user/questions/${question_id}/following`, success, error);
  },

  /**
   * 添加关注
   */
  addFollow(question_id, success, error) {
    put(`/user/questions/${question_id}/following`, success, error);
  },

  /**
   * 取消关注
   */
  deleteFollow(question_id, success, error) {
    del(`/user/questions/${question_id}/following`, success, error);
  },
};
