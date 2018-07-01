import {
  get,
  patch,
} from './util/requestAlias';

export default {
  /**
   * 获取全部设置
   */
  getAll(success) {
    get('/settings', success);
  },

  update(data, success) {
    patch('/settings', data, success);
  },
};
