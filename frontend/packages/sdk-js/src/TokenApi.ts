import { post } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import { TokenResponse } from './models';

interface LoginParams {
  /**
   * 用户名或邮箱
   */
  name: string;
  /**
   * 经过 hash1 加密后的密码
   */
  password: string;
  /**
   * 设备信息
   */
  device?: string;
  /**
   * 图形验证码token。若上一次请求返回了 captcha_token， 则必须传该参数
   */
  captcha_token?: string;
  /**
   * 图形验证码的值。若上一次请求返回了 captcha_token，则必须传该参数
   */
  captcha_code?: string;
}

const className = 'TokenApi';

/**
 * TokenApi
 */
export default {
  /**
   * 生成 Token
   * 通过账号密码登陆，返回 Token 信息。  若登录失败，且返回信息中含参数 &#x60;captcha_token&#x60; 和 &#x60;captcha_image&#x60;， 表示下次调用该接口时，需要用户输入图形验证码，并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   * @param params.UserLoginRequestBody
   */
  login: (params: LoginParams): Promise<TokenResponse> => {
    return post(
      buildURL(`${className}.login`, '/tokens', params),
      buildRequestBody(params, [
        'name',
        'password',
        'device',
        'captcha_token',
        'captcha_code',
      ]),
    );
  },
};
