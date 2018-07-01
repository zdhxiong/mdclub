import $ from 'mdui.JQ';

export default {
  /**
   * 获取话题列表
   * @param data
   * @param success
   */
  getList(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/topics`,
      data,
      success,
    });
  },

  /**
   * 发布话题
   * @param name
   * @param description
   * @param success
   */
  create(name, description, success) {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/topics`,
      data: {
        name,
        description,
      },
      success,
    });
  },

  /**
   * 更新话题信息
   * @param topic_id
   * @param data
   * @param success
   */
  update(topic_id, data, success) {
    $.ajax({
      method: 'patch',
      url: `${window.G_API}/topics/${topic_id}`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },

  /**
   * 上传话题封面
   * @param topic_id
   * @param file
   * @param success
   */
  uploadCover(topic_id, file, success) {
    const data = new FormData();
    data.append('cover', file);

    $.ajax({
      method: 'post',
      url: `${window.G_API}/topics/${topic_id}/cover`,
      contentType: false,
      data,
      success,
    });
  },
};
