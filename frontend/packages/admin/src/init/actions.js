import { location } from '@hyperapp/router';

import Index from '../pages/index/actions';
import Answer from '../pages/answer/actions';
import Answers from '../pages/answers/actions';
import Article from '../pages/article/actions';
import Articles from '../pages/articles/actions';
import Comment from '../pages/comment/actions';
import Comments from '../pages/comments/actions';
import Images from '../pages/images/actions';
import Options from '../pages/options/actions';
import Question from '../pages/question/actions';
import Questions from '../pages/questions/actions';
import Reports from '../pages/reports/actions';
import Topic from '../pages/topic/actions';
import Topics from '../pages/topics/actions';
import User from '../pages/user/actions';
import Users from '../pages/users/actions';

import Appbar from '../components/appbar/actions';
import SearchBar from '../components/searchbar/actions';
import Pagination from '../components/pagination/actions';
import Datatable from '../components/datatable/actions';
import DialogAnswer from '../components/dialog-answer/actions';
import DialogArticle from '../components/dialog-article/actions';
import DialogComment from '../components/dialog-comment/actions';
import DialogQuestion from '../components/dialog-question/actions';
import DialogReporters from '../components/dialog-reporters/actions';
import DialogTopic from '../components/dialog-topic/actions';
import DialogUser from '../components/dialog-user/actions';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  index: Index,
  answer: Answer,
  answers: Answers,
  article: Article,
  articles: Articles,
  comment: Comment,
  comments: Comments,
  images: Images,
  options: Options,
  question: Question,
  questions: Questions,
  reports: Reports,
  topic: Topic,
  topics: Topics,
  user: User,
  users: Users,

  components: {
    appbar: Appbar,
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
