import $ from 'mdui.JQ';

export default {
  /**
   * 上传当前登录用户的头像
   */
  uploadMine(file, success) {
    const data = new FormData();
    data.append('avatar', file);

    $.ajax({
      method: 'post',
      url: `${window.G_API}/user/avatar`,
      contentType: false,
      data,
      success,
    });
  },

  /**
   * 删除当前登录用户的头像
   * @param success
   */
  deleteMine(success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/user/avatar`,
      success,
    });
  },

  /**
   * 删除指定用户的头像
   * @param user_id
   * @param success
   */
  delete(user_id, success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/users/${user_id}/avatar`,
      success,
    });
  },
};
