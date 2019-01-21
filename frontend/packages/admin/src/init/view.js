import { h } from 'hyperapp';
import { Route } from '@hyperapp/router';
import { JQ as $ } from 'mdui';

import Index from '../pages/index/view';
import Answer from '../pages/answer/view';
import Answers from '../pages/answers/view';
import Article from '../pages/article/view';
import Articles from '../pages/articles/view';
import Comment from '../pages/comment/view';
import Comments from '../pages/comments/view';
import Images from '../pages/images/view';
import Options from '../pages/options/view';
import Question from '../pages/question/view';
import Questions from '../pages/questions/view';
import Reports from '../pages/reports/view';
import Topic from '../pages/topic/view';
import Topics from '../pages/topics/view';
import TrashAnswers from '../pages/trash/answers/view';
import TrashArticles from '../pages/trash/articles/view';
import TrashComments from '../pages/trash/comments/view';
import TrashQuestions from '../pages/trash/questions/view';
import TrashTopics from '../pages/trash/topics/view';
import TrashUsers from '../pages/trash/users/view';
import User from '../pages/user/view'
import Users from '../pages/users/view';

import Appbar from '../components/appbar/view';
import Drawer from '../elements/drawer';
import DialogAnswer from '../components/dialog-answer/view';
import DialogArticle from '../components/dialog-article/view';
import DialogComment from '../components/dialog-comment/view';
import DialogQuestion from '../components/dialog-question/view';
import DialogReporters from '../components/dialog-reporters/view';
import DialogTopic from '../components/dialog-topic/view';
import DialogUser from '../components/dialog-user/view';

export default (global_state, global_actions) => (
  <div>
    <Appbar/>
    <Drawer/>
    <Route path={$.path('')} render={Index(global_state, global_actions)}/>
    <Route path={$.path('/answer/:answer_id')} render={Answer(global_state, global_actions)}/>
    <Route path={$.path('/answers')} render={Answers(global_state, global_actions)}/>
    <Route path={$.path('/article/:article_id')} render={Article(global_state, global_actions)}/>
    <Route path={$.path('/articles')} render={Articles(global_state, global_actions)}/>
    <Route path={$.path('/comment/:comment_id')} render={Comment(global_state, global_actions)}/>
    <Route path={$.path('/comments')} render={Comments(global_state, global_actions)}/>
    <Route path={$.path('/images')} render={Images(global_state, global_actions)}/>
    <Route path={$.path('/options')} render={Options(global_state, global_actions)}/>
    <Route path={$.path('/question/:question_id')} render={Question(global_state, global_actions)}/>
    <Route path={$.path('/questions')} render={Questions(global_state, global_actions)}/>
    <Route path={$.path('/reports')} render={Reports(global_state, global_actions)}/>
    <Route path={$.path('/topic/:topic_id')} render={Topic(global_state, global_actions)}/>
    <Route path={$.path('/topics')} render={Topics(global_state, global_actions)}/>
    <Route path={$.path('/trash/answers')} render={TrashAnswers(global_state, global_actions)}/>
    <Route path={$.path('/trash/articles')} render={TrashArticles(global_state, global_actions)}/>
    <Route path={$.path('/trash/comments')} render={TrashComments(global_state, global_actions)}/>
    <Route path={$.path('/trash/questions')} render={TrashQuestions(global_state, global_actions)}/>
    <Route path={$.path('/trash/topics')} render={TrashTopics(global_state, global_actions)}/>
    <Route path={$.path('/trash/users')} render={TrashUsers(global_state, global_actions)}/>
    <Route path={$.path('/user/:user_id')} render={User(global_state, global_actions)}/>
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
