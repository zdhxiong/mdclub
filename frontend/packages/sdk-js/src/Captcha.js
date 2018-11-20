import {
  post,
} from './util/requestAlias';

export default {
  /**
   * 生成验证码
   *
   * POST /captchas
   */
  create(success) {
    post('/captchas', success);
  },
};
