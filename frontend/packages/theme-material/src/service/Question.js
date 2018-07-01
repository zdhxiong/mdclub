import $ from 'mdui.JQ';

export default {

  /**
   * 最近更新的问题列表
   * @param data
   * @param success
   */
  getRecentList(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/questions/recent`,
      data,
      success,
    });
  },

  /**
   * 获取最近热门问题列表
   * @param data
   * @param success
   */
  getPopularList(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/questions/popular`,
      data,
      success,
    });
  },

  /**
   * 获取问题列表
   * @param data
   * @param success
   */
  getList(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/questions`,
      data,
      success,
    });
  },

  /**
   * 创建问题
   * @param data
   * @param success
   */
  create(data, success) {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/questions`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },

  /**
   * 获取指定问题信息
   * @param question_id
   * @param success
   */
  getOne(question_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/questions/${question_id}`,
      success,
    });
  },

  /**
   * 获取指定问题的关注者
   * @param question_id
   * @param data
   * @param success
   */
  getFollowers(question_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/questions/${question_id}/followers`,
      data,
      success,
    });
  },
};
