import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import { Email, EmailResponse } from './models';

interface SendParams {
  email: Email;
}

/**
 * EmailApi
 */
export default {
  /**
   * 🔐发送邮件
   * 用于后台管理员发送邮件，需要管理员权限
   * @param params.email
   */
  send: (params: SendParams): Promise<EmailResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('EmailApi.send', '/emails', params, []);

    return post(url, params.email || {});
  },
};
