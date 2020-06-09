import { location } from 'hyperapp-router';

// 页面
import index from '~/pages/index/state';
import questions from '../pages/questions/state';
import question from '../pages/question/questionState';
import answer from '../pages/question/answerState';
import articles from '../pages/articles/state';
import article from '../pages/article/state';
import topics from '../pages/topics/state';
import topic from '../pages/topic/state';
import users from '../pages/users/state';
import user from '~/pages/user/state';
import notifications from '~/pages/notifications/state';
// import inbox from '../pages/inbox/state';

// 组件
import theme from '~/components/theme/state';
import login from '~/components/login/state';
import register from '~/components/register/state';
import reset from '~/components/reset/state';
import usersDialog from '~/components/users-dialog/state';
import comments from '~/components/comments/state';
import reportDialog from '~/components/report-dialog/state';
import shareDialog from '~/components/share-dialog/state';

export default {
  location: location.state,

  // 页面
  index,
  questions,
  question,
  answer,
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
