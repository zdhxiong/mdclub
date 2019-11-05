import { post } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import { EmailResponse } from './models';

interface SendParams {
  /**
   * 邮箱地址
   */
  email: Array<string>;
  /**
   * 邮件标题
   */
  subject: string;
  /**
   * 邮件内容
   */
  content: string;
}

const className = 'EmailApi';

/**
 * EmailApi
 */
export default {
  /**
   * 🔐发送邮件
   * 用于后台管理员发送邮件，需要管理员权限
   * @param params.Email
   */
  send: (params: SendParams): Promise<EmailResponse> =>
    post(
      buildURL(`${className}.send`, '/emails', params),
      buildRequestBody(params, ['email', 'subject', 'content']),
    ),
};
