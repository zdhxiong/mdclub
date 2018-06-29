import {
  get,
  post,
} from './util/requestAlias';

export default {
  /**
   * 创建回答
   */
  create(question_id, success, error) {
    post(`/questions/${question_id}/answers`, success, error);
  },

  /**
   * 获取所有回答列表
   */
  getList(data, success, error) {
    get('/answers', data, success, error);
  },

  /**
   * 获取指定问题下的回答
   */
  getListByQuestionId(question_id, data, success, error) {
    get(`/questions/${question_id}/answers`, data, success, error);
  },

  /**
   * 获取回答详情
   */
  getOne(answer_id, success, error) {
    get(`/answers/${answer_id}`, success, error);
  },
};
