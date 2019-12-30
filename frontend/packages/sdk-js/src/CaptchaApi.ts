import { postRequest } from './util/requestAlias';
import { buildURL } from './util/requestHandler';
import { CaptchaResponse } from './models';

/**
 * 生成新的图形验证码
 * 生成新的图形验证码
 */
export const generate = (): Promise<CaptchaResponse> =>
  postRequest(buildURL('/captchas', {}));
