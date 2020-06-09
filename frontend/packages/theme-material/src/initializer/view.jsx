import { h } from 'hyperapp';
import cc from 'classcat';
import { Route } from 'hyperapp-router';
import { $body } from 'mdui/es/utils/dom';
import { isUndefined } from 'mdui.jq/es/utils';
import { getCount } from 'mdclub-sdk-js/es/NotificationApi';
import {
  fullPath,
  isPathArticles,
  isPathQuestions,
  isPathTopics,
  isPathUsers,
} from '~/utils/path';
import { emit } from '~/utils/pubsub';
import Index from '~/pages/index/view.jsx';
import Questions from '~/pages/questions/view.jsx';
import QuestionAndAnswer from '~/pages/question/view.jsx';
import Articles from '~/pages/articles/view.jsx';
import Article from '../pages/article/view.jsx';
import Topics from '~/pages/topics/view.jsx';
import Topic from '../pages/topic/view.jsx';
import Users from '~/pages/users/view.jsx';
import User from '~/pages/user/view.jsx';
import Notifications from '~/pages/notifications/view.jsx';
// import Inbox from '../pages/inbox/view';

import Appbar from '~/components/appbar/view.jsx';
import Drawer from '~/components/drawer/view.jsx';
import Login from '~/components/login/view.jsx';
import Register from '~/components/register/view.jsx';
import Reset from '~/components/reset/view.jsx';
import UsersDialog from '~/components/users-dialog/view.jsx';
import ReportDialog from '~/components/report-dialog/view.jsx';
import ShareDialog from '~/components/share-dialog/view.jsx';
import CommentsDialog from '~/components/comments/dialog.jsx';

const onCreate = () => {
  // 根据操作系统的暗色模式设置主题
  const layoutMedia = window.matchMedia('(prefers-color-scheme: dark)');
  const changeLayout = (e) =>
    emit('layout_update', e.matches ? 'dark' : 'light');

  emit('layout_update', layoutMedia.matches ? 'dark' : 'light');

  // IE 的 matchMedia 中不存在 addEventListener 方法
  if (!isUndefined(layoutMedia.addEventListener)) {
    layoutMedia.addEventListener('change', changeLayout);
  } else if (!isUndefined(layoutMedia.addListener)) {
    // safari 不支持 addEventListener，支持 addListener
    layoutMedia.addListener(changeLayout);
  }

  // 轮询加载未读通知数量，每 30 秒加载一次
  const loadNotificationCount = () => {
    getCount().then(({ data: { notification_count } }) => {
      emit('notification_count_update', notification_count);
    });
  };
  setInterval(loadNotificationCount, 30000);
  loadNotificationCount();
};

export default (globalState, globalActions) => {
  const classList = ['mdui-appbar-with-toolbar', 'mg-app'];

  // 应用栏中包含 tab 时
  if (
    isPathQuestions() ||
    isPathArticles() ||
    (isPathTopics() && globalState.user.user) ||
    (isPathUsers() && globalState.user.user)
  ) {
    classList.push('mdui-appbar-with-tab');
  }

  // 暗色模式
  $body[globalState.theme.layout === 'dark' ? 'addClass' : 'removeClass'](
    'mdui-theme-layout-dark',
  );

  return (
    <div class={cc(classList)} oncreate={onCreate}>
      <Appbar
        user={globalState.user.user}
        interviewee={globalState.user.interviewee}
        notifications={globalState.notifications}
      />
      <Drawer
        user={globalState.user.user}
        interviewee={globalState.user.interviewee}
      />

      <Route
        path={fullPath('/')}
        render={Index(globalState.index, globalActions.index)}
      />
      <Route
        path={fullPath('/questions')}
        render={Questions(globalState.questions, globalActions.questions)}
      />
      <Route
        path={fullPath('/questions/:question_id')}
        render={QuestionAndAnswer(globalState.question, globalActions.question)}
      />
      <Route
        path={fullPath('/questions/:question_id/answers/:answer_id')}
        render={QuestionAndAnswer(globalState.answer, globalActions.answer)}
      />
      <Route
        path={fullPath('/articles')}
        render={Articles(globalState.articles, globalActions.articles)}
      />
      <Route
        path={fullPath('/articles/:article_id')}
        render={Article(globalState.article, globalActions.article)}
      />
      <Route
        path={fullPath('/topics')}
        render={Topics(globalState.topics, globalActions.topics)}
      />
      <Route
        path={fullPath('/topics/:topic_id')}
        render={Topic(globalState.topic, globalActions.topic)}
      />
      <Route
        path={fullPath('/users')}
        render={Users(globalState.users, globalActions.users)}
      />
      <Route
        path={fullPath('/users/:user_id')}
        render={User(globalState.user, globalActions.user)}
      />
      <Route
        path={fullPath('/notifications')}
        render={Notifications(
          globalState.notifications,
          globalActions.notifications,
        )}
      />
      {/* <Route
        path={fullPath('/inbox')}
        render={Inbox}
      /> */}

      <If condition={!globalState.user.user}>
        <Login state={globalState.login} actions={globalActions.login} />
        <Register
          state={globalState.register}
          actions={globalActions.register}
        />
        <Reset state={globalState.reset} actions={globalActions.reset} />
      </If>

      <UsersDialog
        state={globalState.usersDialog}
        actions={globalActions.usersDialog}
      />
      <ReportDialog
        state={globalState.reportDialog}
        actions={globalActions.reportDialog}
      />
      <ShareDialog
        state={globalState.shareDialog}
        actions={globalActions.shareDialog}
      />
      <CommentsDialog
        state={globalState.comments}
        actions={globalActions.comments}
      />
    </div>
  );
};
