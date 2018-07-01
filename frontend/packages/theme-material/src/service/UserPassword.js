import $ from 'mdui.JQ';

export default {
  /**
   * 发送密码重置邮件
   * @param data
   * @param success
   */
  sendResetEmail(data, success) {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/user/password/email`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },

  /**
   * 验证邮箱并更新用户密码
   * @param data
   * @param success
   */
  updateByEmail(data, success) {
    $.ajax({
      method: 'put',
      url: `${window.G_API}/user/password`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },
};
