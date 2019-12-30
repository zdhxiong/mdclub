import { postRequest } from './util/requestAlias';
import { buildURL } from './util/requestHandler';
import { CaptchaResponse } from './models';

const className = 'CaptchaApi';

/**
 * 生成新的图形验证码
 * 生成新的图形验证码
 */
export const generate = (): Promise<CaptchaResponse> =>
  postRequest(buildURL(`${className}.generate`, '/captchas', {}));
