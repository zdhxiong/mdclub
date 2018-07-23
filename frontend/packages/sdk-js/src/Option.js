import {
  get,
  patch,
} from './util/requestAlias';

export default {
  /**
   * 获取全部设置
   */
  getAll(success) {
    get('/options', success);
  },

  update(data, success) {
    patch('/options', data, success);
  },
};
