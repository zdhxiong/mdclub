import { location } from '@hyperapp/router';

import Answers from '../pages/answers/actions';
import Articles from '../pages/articles/actions';
import Comments from '../pages/comments/actions';
import Images from '../pages/images/actions';
import Options from '../pages/options/actions';
import Questions from '../pages/questions/actions';
import Reports from '../pages/reports/actions';
import Topics from '../pages/topics/actions';
import TrashAnswers from '../pages/trash/answers/actions';
import TrashArticles from '../pages/trash/articles/actions';
import TrashComments from '../pages/trash/comments/actions';
import TrashQuestions from '../pages/trash/questions/actions';
import TrashTopics from '../pages/trash/topics/actions';
import Users from '../pages/users/actions';

import AppbarLazyComponent from '../lazyComponents/appbar/actions';
import PaginationLazyComponent from '../lazyComponents/pagination/actions';
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
  trash_answers: TrashAnswers,
  trash_articles: TrashArticles,
  trash_comments: TrashComments,
  trash_questions: TrashQuestions,
  trash_topics: TrashTopics,
  users: Users,

  lazyComponents: {
    appbar: AppbarLazyComponent,
    pagination: PaginationLazyComponent,
    datatable: DatatableLazyComponent,
    reportersDialog: ReportersDialogLazyComponent,
    userDialog: UserDialogLazyComponent,
    topicDialog: TopicDialogLazyComponent,
  },
};
