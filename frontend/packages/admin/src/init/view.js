import { h } from 'hyperapp';
import { Route } from '@hyperapp/router';
import { JQ as $ } from 'mdui';

import Index from '../pages/index/view';
import Answers from '../pages/answers/view';
import Articles from '../pages/articles/view';
import Comments from '../pages/comments/view';
import Images from '../pages/images/view';
import Options from '../pages/options/view';
import Questions from '../pages/questions/view';
import Reports from '../pages/reports/view';
import Topics from '../pages/topics/view';
import Users from '../pages/users/view';

import AppBar from '../lazyComponents/appbar/view';
import Drawer from '../components/drawer';
import DialogAnswer from '../lazyComponents/dialog-answer/view';
import DialogArticle from '../lazyComponents/dialog-article/view';
import DialogComment from '../lazyComponents/dialog-comment/view';
import DialogQuestion from '../lazyComponents/dialog-question/view';
import DialogReporters from '../lazyComponents/dialog-reporters/view';
import DialogTopic from '../lazyComponents/dialog-topic/view';
import DialogUser from '../lazyComponents/dialog-user/view';

export default (global_state, global_actions) => (
  <div>
    <AppBar/>
    <Drawer/>
    <Route path={$.path('')} render={Index(global_state, global_actions)}/>
    <Route path={$.path('/answers')} render={Answers(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={Articles(global_state, global_actions)}/>
    <Route path={$.path('/comments')} render={Comments(global_state, global_actions)}/>
    <Route path={$.path('/images')} render={Images(global_state, global_actions)}/>
    <Route path={$.path('/options')} render={Options(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={Questions(global_state, global_actions)}/>
    <Route path={$.path('/reports')} render={Reports(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={Topics(global_state, global_actions)}/>
    <Route path={$.path('/users')} render={Users(global_state, global_actions)}/>

    <DialogAnswer/>
    <DialogArticle/>
    <DialogComment/>
    <DialogQuestion/>
    <DialogReporters/>
    <DialogTopic/>
    <DialogUser/>
  </div>
);
