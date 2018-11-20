import {
  get,
  patch,
} from './util/requestAlias';

export default {
  /**
   * 获取全部设置
   *
   * GET /options
   */
  getAll(success) {
    get('/options', success);
  },

  /**
   * 更新设置
   *
   * PATCH /options
   */
  updateMultiple(data, success) {
    patch('/options', data, success);
  },
};
