import BrowserAdapter from './adapter/BrowserAdapter';
import defaults from './defaults';
import AnswerApi from './AnswerApi';
import ArticleApi from './ArticleApi';
import CaptchaApi from './CaptchaApi';
import CommentApi from './CommentApi';
import EmailApi from './EmailApi';
import ImageApi from './ImageApi';
import OptionApi from './OptionApi';
import QuestionApi from './QuestionApi';
import ReportApi from './ReportApi';
import TokenApi from './TokenApi';
import TopicApi from './TopicApi';
import UserApi from './UserApi';

defaults.adapter = new BrowserAdapter();

export {
  defaults,
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
