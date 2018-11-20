import {
  get,
  post,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取举报列表
   *
   * GET /reports
   */
  getList(data, success) {
    get('/reports', data, success);
  },

  /**
   * 添加举报
   *
   * POST /reports
   */
  create(data, success) {
    post('/reports', data, success);
  },

  /**
   * 删除多个举报
   *
   * DELETE /reports
   */
  deleteMultiple(report_id, success) {
    del('/reports', success);
  },

  /**
   * 删除指定举报
   *
   * DELETE /reports/{report_id}
   */
  deleteOne(report_id, success) {
    del(`/reports/${report_id}`, success);
  },
};
