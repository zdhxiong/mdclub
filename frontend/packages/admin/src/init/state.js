import { location } from '@hyperapp/router';

import Index from '../pages/index/state';
import Answers from '../pages/answers/state';
import Articles from '../pages/articles/state';
import Comments from '../pages/comments/state';
import Images from '../pages/images/state';
import Options from '../pages/options/state';
import Questions from '../pages/questions/state';
import Reports from '../pages/reports/state';
import Topics from '../pages/topics/state';
import Users from '../pages/users/state';

import AppBar from '../lazyComponents/appbar/state';
import SearchBar from '../lazyComponents/searchbar/state';
import Pagination from '../lazyComponents/pagination/state';
import Datatable from '../lazyComponents/datatable/state';
import DialogAnswer from '../lazyComponents/dialog-answer/state';
import DialogArticle from '../lazyComponents/dialog-article/state';
import DialogComment from '../lazyComponents/dialog-comment/state';
import DialogQuestion from '../lazyComponents/dialog-question/state';
import DialogReporters from '../lazyComponents/dialog-reporters/state';
import DialogTopic from '../lazyComponents/dialog-topic/state';
import DialogUser from '../lazyComponents/dialog-user/state';

export default {
  location: location.state,

  index: Index,
  answers: Answers,
  articles: Articles,
  comments: Comments,
  images: Images,
  options: Options,
  questions: Questions,
  reports: Reports,
  topics: Topics,
  users: Users,

  lazyComponents: {
    appBar: AppBar,
    searchBar: SearchBar,
    pagination: Pagination,
    datatable: Datatable,
    dialogAnswer: DialogAnswer,
    dialogArticle: DialogArticle,
    dialogComment: DialogComment,
    dialogQuestion: DialogQuestion,
    dialogReporters: DialogReporters,
    dialogTopic: DialogTopic,
    dialogUser: DialogUser,
  },
};
