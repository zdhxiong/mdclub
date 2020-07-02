import { location } from 'hyperapp-router';

import answers from '~/pages/answers/state';
import articles from '~/pages/articles/state';
import comments from '~/pages/comments/state';
import index from '~/pages/index/state';
import options from '~/pages/options/state';
import questions from '~/pages/questions/state';
import reports from '~/pages/reports/state';
import topics from '~/pages/topics/state';
import users from '~/pages/users/state';

import answer from '~/components/answer/state';
import answerEdit from '~/components/answer-edit/state';
import appbar from '~/components/appbar/state';
import article from '~/components/article/state';
import articleEdit from '~/components/article-edit/state';
import comment from '~/components/comment/state';
import commentEdit from '~/components/comment-edit/state';
import datatable from '~/components/datatable/state';
import pagination from '~/components/pagination/state';
import question from '~/components/question/state';
import questionEdit from '~/components/question-edit/state';
import reporters from '../components/reporters/state';
import searchBar from '~/components/search-bar/state';
import topic from '~/components/topic/state';
import topicEdit from '~/components/topic-edit/state';
import user from '~/components/user/state';
import userEdit from '~/components/user-edit/state';

export default {
  location: location.state,

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
  topic,
  topicEdit,
  user,
  userEdit,
};
