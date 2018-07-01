import { location } from '@hyperapp/router';

import IndexState from '../pages/index/state';
import QuestionsState from '../pages/questions/state';
import QuestionState from '../pages/question/state';
import ArticlesState from '../pages/articles/state';
import ArticleState from '../pages/article/state';
import TopicsState from '../pages/topics/state';
import TopicState from '../pages/topic/state';
import UserState from '../pages/user/state';
import UsersState from '../pages/users/state';
import NotificationsState from '../pages/notifications/state';
import InboxState from '../pages/inbox/state';

import Login from '../components/login/state';
import Register from '../components/register/state';
import Reset from '../components/reset/state';
import UsersDialog from '../components/users-dialog/state';

export default {
  location: location.state,

  theme: {
    primary: '', // 主色
    accent: 'pink', // 强调色
    layout: '', // 布局色
  },

  index: IndexState,
  questions: QuestionsState,
  question: QuestionState,
  topics: TopicsState,
  topic: TopicState,
  articles: ArticlesState,
  article: ArticleState,
  users: UsersState,
  user: UserState,
  notifications: NotificationsState,
  inbox: InboxState,

  components: {
    login: Login,
    register: Register,
    reset: Reset,
    users_dialog: UsersDialog,
  },

  G_API: window.G_API,
  G_ROOT: window.G_ROOT,
};
