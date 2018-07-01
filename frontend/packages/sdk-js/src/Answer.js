import {
  get,
  post,
} from './util/requestAlias';

export default {
  /**
   * 创建回答
   */
  create(question_id, data, success) {
    post(`/questions/${question_id}/answers`, data, success);
  },

  /**
   * 获取所有回答列表
   */
  getList(data, success) {
    get('/answers', data, success);
  },

  /**
   * 获取指定问题下的回答
   */
  getListByQuestionId(question_id, data, success) {
    get(`/questions/${question_id}/answers`, data, success);
  },

  /**
   * 获取回答详情
   */
  getOne(answer_id, success) {
    get(`/answers/${answer_id}`, success);
  },
};
