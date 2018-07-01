import $ from 'mdui.JQ';

export default {

  /**
   * 获取指定用户关注的问题列表
   * @param user_id
   * @param data
   * @param success
   */
  getFollowingQuestions(user_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/questions/following`,
      data,
      success,
    });
  },

  /**
   * 获取登录用户关注的问题
   * @param data
   * @param success
   */
  getMyFollowingQuestions(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/questions/following`,
      data,
      success,
    });
  },

  /**
   * 检查指定用户是否关注了指定话题
   * @param user_id
   * @param question_id
   * @param success
   */
  isFollowing(user_id, question_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}/questions/${question_id}/following`,
      success,
    });
  },

  /**
   * 检查当前用户是否关注了指定问题
   * @param question_id
   * @param success
   */
  isMyFollowing(question_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user/questions/${question_id}/following`,
      success,
    });
  },

  /**
   * 添加关注
   * @param question_id
   * @param success
   */
  addFollow(question_id, success) {
    $.ajax({
      method: 'put',
      url: `${window.G_API}/user/questions/${question_id}/following`,
      success,
    });
  },

  /**
   * 取消关注
   * @param question_id
   * @param success
   */
  deleteFollow(question_id, success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/user/questions/${question_id}/following`,
      success,
    });
  },
};
