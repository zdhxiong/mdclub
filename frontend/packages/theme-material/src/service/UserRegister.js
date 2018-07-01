import $ from 'mdui.JQ';

export default {
  /**
   * 验证邮箱并创建账号
   * @param data
   * @param success
   */
  create(data, success) {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/users`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },

  /**
   * 发送注册验证邮件
   * @param data
   * @param success
   */
  sendEmail(data, success) {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/user/register/email`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },
};
