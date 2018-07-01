import $ from 'mdui.JQ';

export default {
  /**
   * 获取用户列表
   * @param data
   * @param success
   */
  getList(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users`,
      data,
      success,
    });
  },

  /**
   * 获取用户信息
   * @param user_id
   * @param success
   */
  getOne(user_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/users/${user_id}`,
      success,
    });
  },

  /**
   * 获取当前登录用户信息
   * @param success
   */
  getMe(success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/user`,
      success,
    });
  },

  /**
   * 更新当前登陆用户信息
   * @param headline
   * @param success
   */
  updateMe(headline, success) {
    $.ajax({
      method: 'patch',
      url: `${window.G_API}/user`,
      data: JSON.stringify({ headline }),
      success,
    });
  },
};
