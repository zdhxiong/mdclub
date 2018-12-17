import { location } from '@hyperapp/router';

import Index from '../pages/index/actions';
import Answers from '../pages/answers/actions';
import Articles from '../pages/articles/actions';
import Comments from '../pages/comments/actions';
import Images from '../pages/images/actions';
import Options from '../pages/options/actions';
import Questions from '../pages/questions/actions';
import Reports from '../pages/reports/actions';
import Topics from '../pages/topics/actions';
import TrashAnswers from '../pages/trash/answers/actions';
import TrashArticles from '../pages/trash/articles/actions';
import TrashComments from '../pages/trash/comments/actions';
import TrashQuestions from '../pages/trash/questions/actions';
import TrashTopics from '../pages/trash/topics/actions';
import TrashUsers from '../pages/trash/users/actions';
import Users from '../pages/users/actions';

import AppBar from '../lazyComponents/appbar/actions';
import SearchBar from '../lazyComponents/searchbar/actions';
import Pagination from '../lazyComponents/pagination/actions';
import Datatable from '../lazyComponents/datatable/actions';
import DialogAnswer from '../lazyComponents/dialog-answer/actions';
import DialogArticle from '../lazyComponents/dialog-article/actions';
import DialogComment from '../lazyComponents/dialog-comment/actions';
import DialogQuestion from '../lazyComponents/dialog-question/actions';
import DialogReporters from '../lazyComponents/dialog-reporters/actions';
import DialogTopic from '../lazyComponents/dialog-topic/actions';
import DialogUser from '../lazyComponents/dialog-user/actions';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  index: Index,
  answers: Answers,
  articles: Articles,
  comments: Comments,
  images: Images,
  options: Options,
  questions: Questions,
  reports: Reports,
  topics: Topics,
  trashAnswers: TrashAnswers,
  trashArticles: TrashArticles,
  trashComments: TrashComments,
  trashQuestions: TrashQuestions,
  trashTopics: TrashTopics,
  trashUsers: TrashUsers,
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
