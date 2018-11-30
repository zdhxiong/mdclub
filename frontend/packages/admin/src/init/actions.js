import { location } from '@hyperapp/router';

import Answers from '../pages/answers/actions';
import Articles from '../pages/articles/actions';
import Comments from '../pages/comments/actions';
import Images from '../pages/images/actions';
import Options from '../pages/options/actions';
import Questions from '../pages/questions/actions';
import Reports from '../pages/reports/actions';
import Topics from '../pages/topics/actions';
import Trash from '../pages/trash/actions';
import Users from '../pages/users/actions';

import AppbarLazyComponent from '../lazyComponents/appbar/actions';
import DatatableLazyComponent from '../lazyComponents/datatable/actions';
import ReportersDialogLazyComponent from '../lazyComponents/reporters-dialog/actions';
import UserDialogLazyComponent from '../lazyComponents/user-dialog/actions';
import TopicDialogLazyComponent from '../lazyComponents/topic-dialog/actions';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  answers: Answers,
  articles: Articles,
  comments: Comments,
  images: Images,
  options: Options,
  questions: Questions,
  reports: Reports,
  topics: Topics,
  trash: Trash,
  users: Users,

  lazyComponents: {
    appbar: AppbarLazyComponent,
    datatable: DatatableLazyComponent,
    reportersDialog: ReportersDialogLazyComponent,
    userDialog: UserDialogLazyComponent,
    topicDialog: TopicDialogLazyComponent,
  },
};
