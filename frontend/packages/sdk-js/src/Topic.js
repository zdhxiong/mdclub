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
  getList(data, success, error) {
    get('/topics', data, success, error);
  },

  /**
   * 发布话题
   */
  create(name, description, success, error) {
    get('/topics', {
      name,
      description,
    }, success, error);
  },

  /**
   * 更新话题信息
   */
  update(topic_id, data, success, error) {
    patch(`/topics/${topic_id}`, data, success, error);
  },

  /**
   * 上传话题封面
   */
  uploadCover(topic_id, file, success, error) {
    const data = new FormData();
    data.append('cover', file);

    post(`/topics/${topic_id}/cover`, data, success, error);
  },

  /**
   * 获取当前用户关注的话题列表
   */
  getMyFollowing(data, success, error) {
    get('/user/topics/following', data, success, error);
  },

  /**
   * 获取当前用户未关注的话题列表
   */
  getMyNotFollowing(data, success, error) {
    get('/user/topics/not_following', data, success, error);
  },

  /**
   * 获取指定用户关注的话题列表
   */
  getFollowing(user_id, data, success, error) {
    get(`/users/${user_id}/topics/following`, data, success, error);
  },

  /**
   * 获取指定用户未关注的话题列表
   */
  getNotFollowing(user_id, data, success, error) {
    get(`/users/${user_id}/topics/not_following`, data, success, error);
  },

  /**
   * 检查指定用户是否关注了指定话题
   */
  isFollowing(user_id, topic_id, success, error) {
    get(`/users/${user_id}/topics/${topic_id}/following`, success, error);
  },

  /**
   * 检查当前用户是否关注了指定话题
   */
  isMyFollowing(topic_id, success, error) {
    get(`/user/topics/${topic_id}/following`, success, error);
  },

  /**
   * 添加关注
   */
  addFollow(topic_id, success, error) {
    put(`/user/topics/${topic_id}/following`, success, error);
  },

  /**
   * 取消关注
   */
  deleteFollow(topic_id, success, error) {
    del(`/user/topics/${topic_id}/following`, success, error);
  },
};
