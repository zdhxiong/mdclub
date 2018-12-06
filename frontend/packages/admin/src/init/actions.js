import { location } from '@hyperapp/router';

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
import Users from '../pages/users/actions';

import AppBar from '../lazyComponents/appbar/actions';
import SearchBar from '../lazyComponents/searchbar/actions';
import Pagination from '../lazyComponents/pagination/actions';
import Datatable from '../lazyComponents/datatable/actions';
import ImageList from '../lazyComponents/imagelist/actions';
import ReportersDialog from '../lazyComponents/reporters-dialog/actions';
import UserDialog from '../lazyComponents/user-dialog/actions';
import TopicDialog from '../lazyComponents/topic-dialog/actions';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  answers: Answers,
  articles: Articles,
  comments: Comments,
  images: Images,
  options: Options,
  questions: Questions,
  reports: Reports,
  topics: Topics,
  trash_answers: TrashAnswers,
  trash_articles: TrashArticles,
  trash_comments: TrashComments,
  trash_questions: TrashQuestions,
  trash_topics: TrashTopics,
  users: Users,

  lazyComponents: {
    appBar: AppBar,
    searchBar: SearchBar,
    pagination: Pagination,
    datatable: Datatable,
    imageList: ImageList,
    reportersDialog: ReportersDialog,
    userDialog: UserDialog,
    topicDialog: TopicDialog,
  },
};
