import { location } from '@hyperapp/router';

import AnswersState from '../pages/answers/state';
import ArticlesState from '../pages/articles/state';
import CommentsState from '../pages/comments/state';
import ImagesState from '../pages/images/state';
import OptionsState from '../pages/options/state';
import QuestionsState from '../pages/questions/state';
import ReportsState from '../pages/reports/state';
import TopicsState from '../pages/topics/state';
import TrashState from '../pages/trash/state';
import UsersState from '../pages/users/state';

export default {
  location: location.state,

  // 主题，包括 light、dark
  theme: 'light',

  // 当前登录用户信息
  user: {},

  answers: AnswersState,
  articles: ArticlesState,
  comments: CommentsState,
  images: ImagesState,
  options: OptionsState,
  questions: QuestionsState,
  reports: ReportsState,
  topics: TopicsState,
  trash: TrashState,
  users: UsersState,

  components: {

  },
};
