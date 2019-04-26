import {
  post,
} from './util/requestAlias';

export default {
  // 发送邮件
  send: data => post('/emails', data),
};
