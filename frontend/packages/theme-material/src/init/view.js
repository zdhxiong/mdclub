import { h } from 'hyperapp';
import { Route } from '@hyperapp/router';
import cc from 'classcat';
import { JQ as $ } from 'mdui';

import IndexView from '../pages/index/view';
import QuestionsView from '../pages/questions/view';
import QuestionView from '../pages/question/view';
import ArticlesView from '../pages/articles/view';
import ArticleView from '../pages/article/view';
import TopicsView from '../pages/topics/view';
import TopicView from '../pages/topic/view';
import UsersView from '../pages/users/view';
import UserView from '../pages/user/view';
import NotificationsView from '../pages/notifications/view';
import InboxView from '../pages/inbox/view';

import Appbar from '../components/appbar';
import Drawer from '../components/drawer';
import Login from '../components/login/view';
import Register from '../components/register/view';
import Reset from '../components/reset/view';
import UsersDialog from '../components/users-dialog/view';

export default (global_state, global_actions) => (
  <div class={cc([
    'mdui-appbar-with-toolbar',
    global_state.theme.accent ? `mdui-theme-accent-${global_state.theme.accent}` : false,
    global_state.theme.primary ? `mdui-theme-primary-${global_state.theme.primary}` : false,
    global_state.theme.layout ? `mdui-theme-layout-${global_state.theme.layout}` : false,
    {
      'mdui-appbar-with-tab':
      $.isPathMatched('/questions') ||
      $.isPathMatched('/articles') ||
      ($.isPathMatched('/topics') && global_state.user.user.user_id) ||
      ($.isPathMatched('/users') && global_state.user.user.user_id),
    },
  ])}>
    <Appbar/>
    <Drawer/>

    <Route path={$.path('/')} render={IndexView(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={QuestionsView(global_state, global_actions)}/>
    <Route path={$.path('/questions/:question_id')} render={QuestionView(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={ArticlesView(global_state, global_actions)}/>
    <Route path={$.path('/articles/:article_id')} render={ArticleView(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={TopicsView(global_state, global_actions)}/>
    <Route path={$.path('/topics/:topic_id')} render={TopicView(global_state, global_actions)}/>
    <Route path={$.path('/users')} render={UsersView(global_state, global_actions)}/>
    <Route path={$.path('/users/:user_id')} render={UserView(global_state, global_actions)}/>
    <Route path={$.path('/notifications')} render={NotificationsView(global_state, global_actions)}/>
    <Route path={$.path('/inbox')} render={InboxView(global_state, global_actions)}/>

    {!global_state.user.user.user_id ? <Login/> : ''}
    {!global_state.user.user.user_id ? <Register/> : ''}
    {!global_state.user.user.user_id ? <Reset/> : ''}
    <UsersDialog/>
  </div>
);
