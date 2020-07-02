import { h } from 'hyperapp';
import $ from 'mdui.jq';
import { Route } from 'hyperapp-router';
import { isUndefined } from 'mdui.jq/es/utils';
import { fullPath } from '~/utils/path';
import { emit } from '~/utils/pubsub';

// 页面
import Answers from '~/pages/answers/view.jsx';
import Articles from '~/pages/articles/view.jsx';
import Comments from '~/pages/comments/view.jsx';
import Index from '~/pages/index/view.jsx';
import Options from '~/pages/options/view.jsx';
import Option from '~/pages/option/view.jsx';
import Questions from '~/pages/questions/view.jsx';
import Reports from '~/pages/reports/view.jsx';
import Topics from '~/pages/topics/view.jsx';
import Users from '~/pages/users/view.jsx';

// 组件
import Answer from '~/components/answer/view.jsx';
import AnswerEdit from '~/components/answer-edit/view.jsx';
import Appbar from '~/components/appbar/view.jsx';
import Article from '~/components/article/view.jsx';
import ArticleEdit from '~/components/article-edit/view.jsx';
import Comment from '~/components/comment/view.jsx';
import Drawer from '~/components/drawer/view.jsx';
import Question from '~/components/question/view.jsx';
import QuestionEdit from '~/components/question-edit/view.jsx';
import Reporters from '~/components/reporters/view.jsx';
import SearchBar from '~/components/search-bar/view.jsx';
import Topic from '~/components/topic/view.jsx';
import TopicEdit from '~/components/topic-edit/view.jsx';
import User from '~/components/user/view.jsx';
import UserEdit from '~/components/user-edit/view.jsx';

const onCreate = () => {
  // 根据操作系统的暗色模式设置主题
  const layoutMedia = window.matchMedia('(prefers-color-scheme: dark)');
  const changeLayout = (e) =>
    emit('layout_update', e.matches ? 'dark' : 'light');

  emit('layout_update', layoutMedia.matches ? 'dark' : 'light');

  // IE 的 matchMedia 中不存在 addEventListener 方法
  if (!isUndefined(layoutMedia.addEventListener)) {
    $(layoutMedia).on('change', changeLayout);
  }
};

export default (globalState, globalActions) => (
  <div oncreate={onCreate}>
    <Appbar state={globalState.appbar} actions={globalActions.appbar}>
      <SearchBar
        state={globalState.searchBar}
        actions={globalActions.searchBar}
      />
    </Appbar>
    <Drawer />

    <Route
      path={fullPath('/answers')}
      render={Answers(globalState.answers, globalActions.answers)}
    />
    <Route
      path={fullPath('/articles')}
      render={Articles(globalState.articles, globalActions.articles)}
    />
    <Route
      path={fullPath('/comments')}
      render={Comments(globalState.comments, globalActions.comments)}
    />
    <Route
      path={fullPath('')}
      render={Index(globalState.index, globalActions.index)}
    />
    <Route
      path={fullPath('/options')}
      render={Options(globalState.options, globalActions.options)}
    />
    <Route
      path={fullPath('/options/:option')}
      render={Option(globalState.options, globalActions.options)}
    />
    <Route
      path={fullPath('/questions')}
      render={Questions(globalState.questions, globalActions.questions)}
    />
    <Route
      path={fullPath('/reports')}
      render={Reports(globalState.reports, globalActions.reports)}
    />
    <Route
      path={fullPath('/topics')}
      render={Topics(globalState.topics, globalActions.topics)}
    />
    <Route
      path={fullPath('/users')}
      render={Users(globalState.users, globalActions.users)}
    />

    <Answer state={globalState.answer} actions={globalActions.answer} />
    <AnswerEdit
      state={globalState.answerEdit}
      actions={globalActions.answerEdit}
    />
    <Article state={globalState.article} actions={globalActions.article} />
    <ArticleEdit
      state={globalState.articleEdit}
      actions={globalActions.articleEdit}
    />
    <Comment state={globalState.comment} actions={globalActions.comment} />
    <Question state={globalState.question} actions={globalActions.question} />
    <QuestionEdit
      state={globalState.questionEdit}
      actions={globalActions.questionEdit}
    />
    <Reporters
      state={globalState.reporters}
      actions={globalActions.reporters}
    />
    <Topic state={globalState.topic} actions={globalActions.topic} />
    <TopicEdit
      state={globalState.topicEdit}
      actions={globalActions.topicEdit}
    />
    <User state={globalState.user} actions={globalActions.user} />
    <UserEdit state={globalState.userEdit} actions={globalActions.userEdit} />
  </div>
);
