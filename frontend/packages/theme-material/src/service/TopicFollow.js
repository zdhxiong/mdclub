import $ from 'mdui.JQ';

export default {

  /**
   * 获取当前用户关注的话题列表
   * @param data
   * @param success
   */
  getMyFollowingTopics(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/topics/following`,
      data,
      success,
    });
  },

  /**
   * 获取当前用户未关注的话题列表
   * @param data
   * @param success
   */
  getMyNotFollowingTopics(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/topics/not_following`,
      data,
      success,
    });
  },

  /**
   * 获取指定用户关注的话题列表
   * @param user_id
   * @param data
   * @param success
   */
  getFollowingTopics(user_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/topics/following`,
      data,
      success,
    });
  },

  /**
   * 获取指定用户未关注的话题列表
   * @param user_id
   * @param data
   * @param success
   */
  getNotFollowingTopics(user_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/topics/not_following`,
      data,
      success,
    });
  },

  /**
   * 检查指定用户是否关注了指定话题
   * @param user_id
   * @param topic_id
   * @param success
   */
  isFollowing(user_id, topic_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/topics/${topic_id}/following`,
      success,
    });
  },

  /**
   * 检查当前用户是否关注了指定话题
   * @param topic_id
   * @param success
   */
  isMyFollowing(topic_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/topics/${topic_id}/following`,
      success,
    });
  },

  /**
   * 添加关注
   * @param topic_id
   * @param success
   */
  addFollow(topic_id, success) {
    $.ajax({
      method: 'put',
      url: `${window.G_API}/user/topics/${topic_id}/following`,
      success,
    });
  },

  /**
   * 取消关注
   * @param topic_id
   * @param success
   */
  deleteFollow(topic_id, success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/user/topics/${topic_id}/following`,
      success,
    });
  },
};
