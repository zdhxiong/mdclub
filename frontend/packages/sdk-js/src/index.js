import defaults from './util/defaults';
import Answer from './Answer';
import Captcha from './Captcha';
import Email from './Email';
import Question from './Question';
import Setting from './Setting';
import Token from './Token';
import Topic from './Topic';
import User from './User';

export default {
  defaults,
  Answer,
  Captcha,
  Email,
  Question,
  Setting,
  Token,
  Topic,
  User,
};

/**
 * // 设置全局默认参数
 * mdclubAPI.defaults.token = '';
 * mdclubAPI.defaults.baseURL = '';
 * mdclubAPI.defaults.beforeSend = function () {}
 * mdclubAPI.defaults.success = function () {}
 * mdclubAPI.defaults.error = function () {}
 * mdclubAPI.defaults.complete = function () {}
 */
