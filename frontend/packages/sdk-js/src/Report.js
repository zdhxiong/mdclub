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
   * 删除多个举报
   *
   * DELETE /reports
   */
  deleteMultiple(target, success) {
    del('/reports', success);
  },

  /**
   * 获取举报详情列表
   *
   * GET /reports/{reportable_type}/{reportable_id}
   */
  getDetailList(reportable_type, reportable_id, data, success) {
    get(`/reports/${reportable_type}/${reportable_id}`, data, success);
  },

  /**
   * 添加举报
   *
   * POST /reports/{reportable_type}/{reportable_id}
   */
  create(reportable_type, reportable_id, reason, success) {
    post(`/reports/${reportable_type}/${reportable_id}`, { reason }, success);
  },



  /**
   * 删除举报
   *
   * DELETE /reports/{reportable_type}/{reportable_id}
   */
  deleteOne(reportable_type, reportable_id, success) {
    del(`/reports/${reportable_type}/${reportable_id}`, success);
  },
};
