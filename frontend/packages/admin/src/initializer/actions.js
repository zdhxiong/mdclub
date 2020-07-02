import { location } from 'hyperapp-router';

// 页面
import answers from '~/pages/answers/actions';
import articles from '~/pages/articles/actions';
import comments from '~/pages/comments/actions';
import index from '~/pages/index/actions';
import options from '~/pages/options/actions';
import questions from '~/pages/questions/actions';
import reports from '~/pages/reports/actions';
import topics from '~/pages/topics/actions';
import users from '~/pages/users/actions';

// 组件
import answer from '~/components/answer/actions';
import answerEdit from '~/components/answer-edit/actions';
import appbar from '~/components/appbar/actions';
import article from '~/components/article/actions';
import articleEdit from '~/components/article-edit/actions';
import comment from '~/components/comment/actions';
import commentEdit from '~/components/comment-edit/actions';
import datatable from '~/components/datatable/actions';
import pagination from '~/components/pagination/actions';
import question from '~/components/question/actions';
import questionEdit from '~/components/question-edit/actions';
import reporters from '~/components/reporters/actions';
import searchBar from '~/components/search-bar/actions';
import theme from '~/components/theme/actions';
import topic from '~/components/topic/actions';
import topicEdit from '~/components/topic-edit/actions';
import user from '~/components/user/actions';
import userEdit from '~/components/user-edit/actions';

export default {
  location: location.actions,
  getState: () => (state) => state,

  // 页面
  answers,
  articles,
  comments,
  index,
  options, // options 和 option 共用
  questions,
  reports,
  topics,
  users,

  // 组件
  answer,
  answerEdit,
  appbar,
  article,
  articleEdit,
  comment,
  commentEdit,
  datatable,
  pagination,
  question,
  questionEdit,
  reporters,
  searchBar,
  theme,
  topic,
  topicEdit,
  user,
  userEdit,
};
