import { location } from '@hyperapp/router';

import Index from '../pages/index/state';
import Answer from '../pages/answer/state';
import Answers from '../pages/answers/state';
import Article from '../pages/article/state';
import Articles from '../pages/articles/state';
import Comment from '../pages/comment/state';
import Comments from '../pages/comments/state';
import Images from '../pages/images/state';
import Options from '../pages/options/state';
import Question from '../pages/question/state';
import Questions from '../pages/questions/state';
import Reports from '../pages/reports/state';
import Topic from '../pages/topic/state';
import Topics from '../pages/topics/state';
import TrashAnswers from '../pages/trash/answers/state';
import TrashArticles from '../pages/trash/articles/state';
import TrashComments from '../pages/trash/comments/state';
import TrashQuestions from '../pages/trash/questions/state';
import TrashTopics from '../pages/trash/topics/state';
import TrashUsers from '../pages/trash/users/state';
import User from '../pages/user/state';
import Users from '../pages/users/state';

import Appbar from '../components/appbar/state';
import SearchBar from '../components/searchbar/state';
import Pagination from '../components/pagination/state';
import Datatable from '../components/datatable/state';
import DialogAnswer from '../components/dialog-answer/state';
import DialogArticle from '../components/dialog-article/state';
import DialogComment from '../components/dialog-comment/state';
import DialogQuestion from '../components/dialog-question/state';
import DialogReporters from '../components/dialog-reporters/state';
import DialogTopic from '../components/dialog-topic/state';
import DialogUser from '../components/dialog-user/state';

export default {
  location: location.state,

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
  trashAnswers: TrashAnswers,
  trashArticles: TrashArticles,
  trashComments: TrashComments,
  trashQuestions: TrashQuestions,
  trashTopics: TrashTopics,
  trashUsers: TrashUsers,
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
