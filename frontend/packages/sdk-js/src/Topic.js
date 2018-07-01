import {
  get,
  post,
  patch,
  put,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取话题列表
   */
  getList(data, success) {
    get('/topics', data, success);
  },

  /**
   * 发布话题
   */
  create(name, description, success) {
    get('/topics', {
      name,
      description,
    }, success);
  },

  /**
   * 更新话题信息
   */
  update(topic_id, data, success) {
    patch(`/topics/${topic_id}`, data, success);
  },

  /**
   * 上传话题封面
   */
  uploadCover(topic_id, file, success) {
    const data = new FormData();
    data.append('cover', file);

    post(`/topics/${topic_id}/cover`, data, success);
  },

  /**
   * 获取当前用户关注的话题列表
   */
  getMyFollowing(data, success) {
    get('/user/topics/following', data, success);
  },

  /**
   * 获取当前用户未关注的话题列表
   */
  getMyNotFollowing(data, success) {
    get('/user/topics/not_following', data, success);
  },

  /**
   * 获取指定用户关注的话题列表
   */
  getFollowing(user_id, data, success) {
    get(`/users/${user_id}/topics/following`, data, success);
  },

  /**
   * 获取指定用户未关注的话题列表
   */
  getNotFollowing(user_id, data, success) {
    get(`/users/${user_id}/topics/not_following`, data, success);
  },

  /**
   * 检查指定用户是否关注了指定话题
   */
  isFollowing(user_id, topic_id, success) {
    get(`/users/${user_id}/topics/${topic_id}/following`, success);
  },

  /**
   * 检查当前用户是否关注了指定话题
   */
  isMyFollowing(topic_id, success) {
    get(`/user/topics/${topic_id}/following`, success);
  },

  /**
   * 添加关注
   */
  addFollow(topic_id, success) {
    put(`/user/topics/${topic_id}/following`, success);
  },

  /**
   * 取消关注
   */
  deleteFollow(topic_id, success) {
    del(`/user/topics/${topic_id}/following`, success);
  },
};
