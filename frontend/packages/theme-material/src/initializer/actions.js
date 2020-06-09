import { location } from 'hyperapp-router';

// 页面
import index from '~/pages/index/actions';
import questions from '../pages/questions/actions';
import questionAndAnswer from '../pages/question/actions';
import articles from '../pages/articles/actions';
import article from '../pages/article/actions';
import topics from '../pages/topics/actions';
import topic from '../pages/topic/actions';
import users from '../pages/users/actions';
import user from '~/pages/user/actions';
import notifications from '~/pages/notifications/actions';
// import inbox from '../pages/inbox/actions';

// 组件
import theme from '~/components/theme/actions';
import login from '~/components/login/actions';
import register from '~/components/register/actions';
import reset from '~/components/reset/actions';
import usersDialog from '~/components/users-dialog/actions';
import comments from '~/components/comments/actions';
import reportDialog from '~/components/report-dialog/actions';
import shareDialog from '~/components/share-dialog/actions';

export default {
  location: location.actions,
  getState: () => (state) => state,

  // 页面
  index,
  questions,
  question: questionAndAnswer,
  answer: questionAndAnswer,
  articles,
  article,
  topics,
  topic,
  users,
  user,
  notifications,
  // inbox,

  // 组件
  theme,
  login,
  register,
  reset,
  usersDialog,
  comments,
  reportDialog,
  shareDialog,
};
