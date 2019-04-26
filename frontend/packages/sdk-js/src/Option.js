import {
  get,
  patch,
} from './util/requestAlias';

export default {
  // 获取全部设置
  getAll: () => get('/options'),

  // 更新设置
  updateMultiple: data => patch('/options', data),
};
