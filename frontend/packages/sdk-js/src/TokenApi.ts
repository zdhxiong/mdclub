import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import { UserLoginRequestBody, TokenResponse } from './models';

interface LoginParams {
  userLoginRequestBody: UserLoginRequestBody;
}

/**
 * TokenApi
 */
export default {
  /**
   * 生成 Token
   * 通过账号密码登陆，返回 Token 信息。  若登录失败，且返回信息中含参数 &#x60;captcha_token&#x60; 和 &#x60;captcha_image&#x60;， 表示下次调用该接口时，需要用户输入图形验证码，并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   * @param params.userLoginRequestBody
   */
  login: (params: LoginParams): Promise<TokenResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TokenApi.login', '/tokens', params, []);

    return post(url, params.userLoginRequestBody || {});
  },
};
