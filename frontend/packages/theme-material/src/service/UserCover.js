import $ from 'mdui.JQ';

export default {
  /**
   * 上传当前登录用户的封面
   */
  uploadMine(file, success) {
    const data = new FormData();
    data.append('cover', file);

    $.ajax({
      method: 'post',
      url: `${window.G_API}/user/cover`,
      contentType: false,
      data,
      success,
    });
  },

  /**
   * 删除当前登录用户的封面
   * @param success
   */
  deleteMine(success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/user/cover`,
      success,
    });
  },

  /**
   * 删除指定用户的封面
   * @param user_id
   * @param success
   */
  delete(user_id, success) {
    $.ajax({
      method: 'delete',
      url: `${window.G_API}/users/${user_id}/cover`,
      success,
    });
  },
};
