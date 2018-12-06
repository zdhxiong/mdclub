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
import TrashAnswers from '../pages/trash/answers/view';
import TrashArticles from '../pages/trash/articles/view';
import TrashComments from '../pages/trash/comments/view';
import TrashQuestions from '../pages/trash/questions/view';
import TrashTopics from '../pages/trash/topics/view';
import Users from '../pages/users/view';

import AppBar from '../lazyComponents/appbar/view';
import Drawer from '../components/drawer';
import ReportersDialog from '../lazyComponents/dialog-reporters/view';
import UserDialog from '../lazyComponents/dialog-user/view';
import TopicDialog from '../lazyComponents/dialog-topic/view';

export default (global_state, global_actions) => (
  <div class="mdui-appbar-with-toolbar mdui-theme-primary-blue mdui-theme-accent-blue">
    <AppBar/>
    <Drawer/>
    <Route path={$.path('/answers')} render={Answers(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={Articles(global_state, global_actions)}/>
    <Route path={$.path('/comments')} render={Comments(global_state, global_actions)}/>
    <Route path={$.path('/images')} render={Images(global_state, global_actions)}/>
    <Route path={$.path('/options')} render={Options(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={Questions(global_state, global_actions)}/>
    <Route path={$.path('/reports')} render={Reports(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={Topics(global_state, global_actions)}/>
    <Route path={$.path('/trash/answers')} render={TrashAnswers(global_state, global_actions)}/>
    <Route path={$.path('/trash/articles')} render={TrashArticles(global_state, global_actions)}/>
    <Route path={$.path('/trash/comments')} render={TrashComments(global_state, global_actions)}/>
    <Route path={$.path('/trash/questions')} render={TrashQuestions(global_state, global_actions)}/>
    <Route path={$.path('/trash/topics')} render={TrashTopics(global_state, global_actions)}/>
    <Route path={$.path('/users')} render={Users(global_state, global_actions)}/>

    <ReportersDialog/>
    <UserDialog/>
    <TopicDialog/>
  </div>
);
