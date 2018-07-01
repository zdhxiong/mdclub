import $ from 'mdui.JQ';

export default {

  /**
   * 创建回答
   * @param question_id
   * @param data
   * @param success
   */
  create(question_id, data, success) {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/questions/${question_id}/answers`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },

  /**
   * 获取所有回答列表
   * @param data
   * @param success
   */
  getList(data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/answers`,
      data,
      success,
    });
  },

  /**
   * 获取指定问题下的回答
   * @param question_id
   * @param data
   * @param success
   */
  getListByQuestionId(question_id, data, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/questions/${question_id}/answers`,
      data,
      success,
    });
  },

  /**
   * 获取回答详情
   * @param answer_id
   * @param success
   */
  getOne(answer_id, success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/answers/${answer_id}`,
      success,
    });
  },
};
