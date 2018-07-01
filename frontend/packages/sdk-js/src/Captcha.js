import {
  post,
} from './util/requestAlias';

export default {
  /**
   * 生成验证码
   */
  create(success) {
    post('/captchas', success);
  },
};
