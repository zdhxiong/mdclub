import { h } from 'hyperapp';
import { Route } from '@hyperapp/router';
import cc from 'classcat';
import { JQ as $ } from 'mdui';

import AnswersView from '../pages/answers/view';
import ArticlesView from '../pages/articles/view';
import CommentsView from '../pages/comments/view';
import ImagesView from '../pages/images/view';
import OptionsView from '../pages/options/view';
import QuestionsView from '../pages/questions/view';
import ReportsView from '../pages/reports/view';
import TopicsView from '../pages/topics/view';
import TrashView from '../pages/trash/view';
import UsersView from '../pages/users/view';

import Appbar from '../components/appbar';
import Drawer from '../components/drawer';

export default (global_state, global_actions) => (
  <div class={cc([
    'mdui-appbar-with-toolbar mdui-theme-accent-blue',
    global_state.theme === 'dark' ? 'mdui-theme-layout-dark' : false,
  ])}>

    <Appbar/>
    <Drawer/>

    <Route path={$.path('/answers')} render={AnswersView(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={ArticlesView(global_state, global_actions)}/>
    <Route path={$.path('/comments')} render={CommentsView(global_state, global_actions)}/>
    <Route path={$.path('/images')} render={ImagesView(global_state, global_actions)}/>
    <Route path={$.path('/options')} render={OptionsView(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={QuestionsView(global_state, global_actions)}/>
    <Route path={$.path('/reports')} render={ReportsView(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={TopicsView(global_state, global_actions)}/>
    <Route path={$.path('/trash')} render={TrashView(global_state, global_actions)}/>
    <Route path={$.path('/users')} render={UsersView(global_state, global_actions)}/>

  </div>
);
