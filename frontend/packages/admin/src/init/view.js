import { h } from 'hyperapp';
import { Route } from '@hyperapp/router';
import { JQ as $ } from 'mdui';

import Index from '../pages/index/view';
import Answers from '../pages/answers/view';
import Articles from '../pages/articles/view';
import Comments from '../pages/comments/view';
import Options from '../pages/options/view';
import Questions from '../pages/questions/view';
import Reports from '../pages/reports/view';
import Topics from '../pages/topics/view';
import Users from '../pages/users/view';

import Appbar from '../components/appbar/view';
import Drawer from '../elements/drawer';
import Reporters from '../components/reporters/view';
import Topic from '../components/topic/view';
import TopicEdit from '../components/topic-edit/view';
import User from '../components/user/view';

export default (global_state, global_actions) => (
  <div>
    <Appbar/>
    <Drawer/>

    <Route path={$.path('')} render={Index(global_state, global_actions)}/>
    <Route path={$.path('/answers')} render={Answers(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={Articles(global_state, global_actions)}/>
    <Route path={$.path('/comments')} render={Comments(global_state, global_actions)}/>
    <Route path={$.path('/options')} render={Options(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={Questions(global_state, global_actions)}/>
    <Route path={$.path('/reports')} render={Reports(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={Topics(global_state, global_actions)}/>
    <Route path={$.path('/users')} render={Users(global_state, global_actions)}/>

    <Reporters/>
    <Topic/>
    <TopicEdit/>
    <User/>
  </div>
);
