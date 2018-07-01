import { location } from '@hyperapp/router';
import mdui, { JQ as $ } from 'mdui';

import IndexActions from '../pages/index/actions';
import QuestionsActions from '../pages/questions/actions';
import QuestionActions from '../pages/question/actions';
import ArticlesActions from '../pages/articles/actions';
import ArticleActions from '../pages/article/actions';
import TopicsActions from '../pages/topics/actions';
import TopicActions from '../pages/topic/actions';
import UsersActions from '../pages/users/actions';
import UserActions from '../pages/user/actions';
import NotificationsActions from '../pages/notifications/actions';
import InboxActions from '../pages/inbox/actions';

import Login from '../components/login/actions';
import Register from '../components/register/actions';
import Reset from '../components/reset/actions';
import UsersDialog from '../components/users-dialog/actions';

import colorHelper from '../helper/color';

export default {
  location: location.actions,

  // 设置主题
  theme: {
    setPrimary: (color) => {
      $('meta[name="theme-color"]').attr('content', colorHelper.primary[color]);

      return { primary: color };
    },
    setAccent: color => ({ accent: color }),
    setLayout: color => ({ layout: color }),
  },

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  // 路由切换后的回调
  routeChange: (pathname) => {
    // 回到页面顶部
    window.scrollTo(0, 0);

    // 在手机和平板时，切换路由后关闭抽屉导航
    if (window.innerWidth < 1024) {
      (new mdui.Drawer('.mc-drawer')).close();
    }
  },

  index: IndexActions,
  questions: QuestionsActions,
  question: QuestionActions,
  articles: ArticlesActions,
  article: ArticleActions,
  topics: TopicsActions,
  topic: TopicActions,
  users: UsersActions,
  user: UserActions,
  notifications: NotificationsActions,
  inbox: InboxActions,

  components: {
    login: Login,
    register: Register,
    reset: Reset,
    users_dialog: UsersDialog,
  },
};
