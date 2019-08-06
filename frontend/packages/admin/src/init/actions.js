import { location } from '@hyperapp/router';

import index from '../pages/index/actions';
import answers from '../pages/answers/actions';
import articles from '../pages/articles/actions';
import comments from '../pages/comments/actions';
import options from '../pages/options/actions';
import questions from '../pages/questions/actions';
import reports from '../pages/reports/actions';
import topics from '../pages/topics/actions';
import users from '../pages/users/actions';

import appbar from '../components/appbar/actions';
import searchBar from '../components/searchbar/actions';
import pagination from '../components/pagination/actions';
import datatable from '../components/datatable/actions';
import answer from '../components/answer/actions';
import article from '../components/article/actions';
import comment from '../components/comment/actions';
import question from '../components/question/actions';
import reporters from '../components/reporters/actions';
import topic from '../components/topic/actions';
import topicEdit from '../components/topic-edit/actions';
import user from '../components/user/actions';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  answers,
  articles,
  comments,
  index,
  options,
  questions,
  reports,
  topics,
  users,

  components: {
    answer,
    appbar,
    article,
    comment,
    datatable,
    pagination,
    question,
    reporters,
    searchBar,
    topic,
    topicEdit,
    user,
  },
};
