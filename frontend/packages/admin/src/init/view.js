import { h } from 'hyperapp';
import { Route } from '@hyperapp/router';
import { JQ as $ } from 'mdui';

import Answers from '../pages/answers/view';
import Articles from '../pages/articles/view';
import Comments from '../pages/comments/view';
import Images from '../pages/images/view';
import Options from '../pages/options/view';
import Questions from '../pages/questions/view';
import Reports from '../pages/reports/view';
import Topics from '../pages/topics/view';
import Trash from '../pages/trash/view';
import Users from '../pages/users/view';

import AppbarLazyComponent from '../lazyComponents/appbar/view';
import DrawerComponent from '../components/drawer';
import UserDialogLazyComponent from '../lazyComponents/user-dialog/view';
import ReportersDialogLazyComponent from '../lazyComponents/reporters-dialog/view';

export default (global_state, global_actions) => (
  <div class="mdui-appbar-with-toolbar mdui-theme-primary-blue mdui-theme-accent-blue">
    <AppbarLazyComponent/>
    <DrawerComponent/>
    <Route path={$.path('/answers')} render={Answers(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={Articles(global_state, global_actions)}/>
    <Route path={$.path('/comments')} render={Comments(global_state, global_actions)}/>
    <Route path={$.path('/images')} render={Images(global_state, global_actions)}/>
    <Route path={$.path('/options')} render={Options(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={Questions(global_state, global_actions)}/>
    <Route path={$.path('/reports')} render={Reports(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={Topics(global_state, global_actions)}/>
    <Route path={$.path('/trash')} render={Trash(global_state, global_actions)}/>
    <Route path={$.path('/users')} render={Users(global_state, global_actions)}/>

    <UserDialogLazyComponent/>
    <ReportersDialogLazyComponent/>
  </div>
);
