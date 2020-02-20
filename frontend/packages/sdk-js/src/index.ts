import BrowserAdapter from './adapter/BrowserAdapter';
import defaults from './defaults';
import * as errors from './errors';
import * as AnswerApi from './AnswerApi';
import * as ArticleApi from './ArticleApi';
import * as CaptchaApi from './CaptchaApi';
import * as CommentApi from './CommentApi';
import * as EmailApi from './EmailApi';
import * as ImageApi from './ImageApi';
import * as OptionApi from './OptionApi';
import * as QuestionApi from './QuestionApi';
import * as ReportApi from './ReportApi';
import * as TokenApi from './TokenApi';
import * as TopicApi from './TopicApi';
import * as UserApi from './UserApi';

defaults.adapter = new BrowserAdapter();

export {
  defaults,
  errors,
  AnswerApi,
  ArticleApi,
  CaptchaApi,
  CommentApi,
  EmailApi,
  ImageApi,
  OptionApi,
  QuestionApi,
  ReportApi,
  TokenApi,
  TopicApi,
  UserApi,
};
