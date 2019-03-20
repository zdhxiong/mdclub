import { location } from '@hyperapp/router';

import index from '../pages/index/state';
import answers from '../pages/answers/state';
import articles from '../pages/articles/state';
import comments from '../pages/comments/state';
import options from '../pages/options/state';
import questions from '../pages/questions/state';
import reports from '../pages/reports/state';
import topics from '../pages/topics/state';
import users from '../pages/users/state';

import appbar from '../components/appbar/state';
import searchBar from '../components/searchbar/state';
import pagination from '../components/pagination/state';
import datatable from '../components/datatable/state';
import answer from '../components/answer/state';
import article from '../components/article/state';
import comment from '../components/comment/state';
import question from '../components/question/state';
import reporters from '../components/reporters/state';
import topic from '../components/topic/state';
import topicEdit from '../components/topic-edit/state';
import user from '../components/user/state';

export default {
  location: location.state,

  index,
  answers,
  articles,
  comments,
  options,
  questions,
  reports,
  topics,
  users,

  components: {
    appbar,
    searchBar,
    pagination,
    datatable,
    answer,
    article,
    comment,
    question,
    reporters,
    topic,
    topicEdit,
    user,
  },
};
