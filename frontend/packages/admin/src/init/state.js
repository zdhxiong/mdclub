import { location } from '@hyperapp/router';

import Answers from '../pages/answers/state';
import Articles from '../pages/articles/state';
import Comments from '../pages/comments/state';
import Images from '../pages/images/state';
import Options from '../pages/options/state';
import Questions from '../pages/questions/state';
import Reports from '../pages/reports/state';
import Topics from '../pages/topics/state';
import TrashAnswers from '../pages/trash/answers/state';
import TrashArticles from '../pages/trash/articles/state';
import TrashComments from '../pages/trash/comments/state';
import TrashQuestions from '../pages/trash/questions/state';
import TrashTopics from '../pages/trash/topics/state';
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
  trash_answers: TrashAnswers,
  trash_articles: TrashArticles,
  trash_comments: TrashComments,
  trash_questions: TrashQuestions,
  trash_topics: TrashTopics,
  users: Users,

  lazyComponents: {
    appbar: AppbarLazyComponent,
    datatable: DatatableLazyComponent,
    reportersDialog: ReportersDialogLazyComponent,
    userDialog: UserDialogLazyComponent,
    topicDialog: TopicDialogLazyComponent,
  },
};
