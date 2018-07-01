import $ from 'mdui.JQ';

export default {
  /**
   * 获取当前登录用户的关注者
   * @param data
   * @param success
   */
  getMyFollowers(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/followers`,
      data,
      success,
    });
  },

  /**
   * 获取指定用户的关注者
   * @param user_id
   * @param data
   * @param success
   */
  getFollowers(user_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/followers`,
      data,
      success,
    });
  },

  /**
   * 获取当前登录用户关注的用户
   * @param data
   * @param success
   */
  getMyFollowing(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/following`,
      data,
      success,
    });
  },

  /**
   * 获取当前登录用户未关注的用户
   * @param data
   * @param success
   */
  getMyNotFollowing(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/not_following`,
      data,
      success,
    });
  },

  /**
   * 获取指定用户关注的用户
   * @param user_id
   * @param data
   * @param success
   */
  getFollowing(user_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/following`,
      data,
      success,
    });
  },

  /**
   * 获取指定用户未关注的用户
   * @param user_id
   * @param data
   * @param success
   */
  getNotFollowing(user_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/not_following`,
      data,
      success,
    });
  },

  /**
   * 检查当前登录用户是否关注了指定用户
   * @param user_id
   * @param success
   */
  isMyFollowing(user_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/following/${user_id}`,
      success,
    });
  },

  /**
   * 检查指定用户是否关注了另一用户
   * @param user_id
   * @param target_user_id
   * @param success
   */
  isFollowing(user_id, target_user_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/following/${target_user_id}`,
      success,
    });
  },

  /**
   * 添加关注
   * @param user_id
   * @param success
   */
  addFollow(user_id, success) {
    $.ajax({
      method: 'put',
      url: `${window.G_API}/user/following/${user_id}`,
      success,
    });
  },

  /**
   * 取消关注
   * @param user_id
   * @param success
   */
  deleteFollow(user_id, success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/user/following/${user_id}`,
      success,
    });
  },
};
