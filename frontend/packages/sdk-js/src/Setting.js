import {
  get,
  patch,
} from './util/requestAlias';

export default {
  /**
   * 获取全部设置
   */
  getAll(success, error) {
    get('/settings', success, error);
  },

  update(data, success, error) {
    patch('/settings', data, success, error);
  },
};
