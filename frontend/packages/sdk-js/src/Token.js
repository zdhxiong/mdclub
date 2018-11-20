import sha1 from 'sha-1';
import {
  post,
} from './util/requestAlias';

export default {
  /**
   * 生成 token
   *
   * POST /tokens
   */
  create(_data, success) {
    const data = _data;
    data.password = sha1(data.password);
    post('/tokens', data, success);
  },
};
