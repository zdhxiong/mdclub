import { post } from './util/requestAlias';
import { buildURL } from './util/requestHandler';
import { CaptchaResponse } from './models';

const className = 'CaptchaApi';

/**
 * CaptchaApi
 */
export default {
  /**
   * 生成新的图形验证码
   */
  generate: (): Promise<CaptchaResponse> => {
    return post(buildURL(`${className}.generate`, '/captchas', {}));
  },
};
