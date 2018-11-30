import { location } from '@hyperapp/router';

import Answers from '../pages/answers/state';
import Articles from '../pages/articles/state';
import Comments from '../pages/comments/state';
import Images from '../pages/images/state';
import Options from '../pages/options/state';
import Questions from '../pages/questions/state';
import Reports from '../pages/reports/state';
import Topics from '../pages/topics/state';
import Trash from '../pages/trash/state';
import Users from '../pages/users/state';

import AppbarLazyComponent from '../lazyComponents/appbar/state';
import DatatableLazyComponent from '../lazyComponents/datatable/state';
import ReportersDialogLazyComponent from '../lazyComponents/reporters-dialog/state';
import UserDialogLazyComponent from '../lazyComponents/user-dialog/state';
import TopicDialogLazyComponent from '../lazyComponents/topic-dialog/state';

export default {
  location: location.state,

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
