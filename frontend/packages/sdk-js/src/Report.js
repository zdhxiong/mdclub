import {
  get,
  post,
  del,
} from './util/requestAlias';

export default {
  // 获取举报列表
  getList: data => get('/reports', data),

  // 删除多个举报
  deleteMultiple: target => del('/reports', { target }),

  // 获取举报详情列表
  getDetailList: (reportable_type, reportable_id, data) => get(`/reports/${reportable_type}/${reportable_id}`, data),

  // 添加举报
  create: (reportable_type, reportable_id, reason) => post(`/reports/${reportable_type}/${reportable_id}`, { reason }),

  // 删除举报
  deleteOne: (reportable_type, reportable_id) => del(`/reports/${reportable_type}/${reportable_id}`),
};
