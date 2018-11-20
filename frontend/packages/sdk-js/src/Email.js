import {
  post,
} from './util/requestAlias';

export default {
  /**
   * 发送邮件
   *
   * POST /emails
   */
  send(data, success) {
    post('/emails', success);
  },
};
