import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import { CaptchaResponse } from './models';

interface GenerateParams {}

/**
 * CaptchaApi
 */
export default {
  /**
   * 生成新的图形验证码
   */
  generate: (): Promise<CaptchaResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CaptchaApi.generate', '/captchas', {}, []);

    return post(url);
  },
};
