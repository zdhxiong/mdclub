import defaults from './util/defaults';
import Answer from './Answer';
import Article from './Article';
import Captcha from './Captcha';
import Comment from './Comment';
import Email from './Email';
import Image from './Image';
import Option from './Option';
import Question from './Question';
import Report from './Report';
import Token from './Token';
import Topic from './Topic';
import User from './User';

export {
  defaults,
  Answer,
  Article,
  Captcha,
  Comment,
  Email,
  Image,
  Option,
  Question,
  Report,
  Token,
  Topic,
  User,
};

/**
 * // 设置全局默认参数
 * mdclubSDK.defaults.token = '';
 * mdclubSDK.defaults.baseURL = '';
 * mdclubSDK.defaults.beforeSend = function () {}
 * mdclubSDK.defaults.success = function (responseData) {}
 * mdclubSDK.defaults.error = function (errorMsg) {}
 * mdclubSDK.defaults.complete = function () {}
 */
