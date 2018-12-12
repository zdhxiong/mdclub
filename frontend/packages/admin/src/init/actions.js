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
import Users from '../pages/users/actions';

import AppBar from '../lazyComponents/appbar/actions';
import SearchBar from '../lazyComponents/searchbar/actions';
import Pagination from '../lazyComponents/pagination/actions';
import Datatable from '../lazyComponents/datatable/actions';
import ImageList from '../lazyComponents/imagelist/actions';
import DialogReporters from '../lazyComponents/dialog-reporters/actions';
import DialogUser from '../lazyComponents/dialog-user/actions';
import DialogTopic from '../lazyComponents/dialog-topic/actions';

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
  users: Users,

  lazyComponents: {
    appBar: AppBar,
    searchBar: SearchBar,
    pagination: Pagination,
    datatable: Datatable,
    imageList: ImageList,
    dialogReporters: DialogReporters,
    dialogUser: DialogUser,
    dialogTopic: DialogTopic,
  },
};
