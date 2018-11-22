import { location } from '@hyperapp/router';
import mdui, { JQ as $ } from 'mdui';

import AnswersActions from '../pages/answers/actions';
import ArticlesActions from '../pages/articles/actions';
import CommentsActions from '../pages/comments/actions';
import ImagesActions from '../pages/images/actions';
import OptionsActions from '../pages/options/actions';
import QuestionsActions from '../pages/questions/actions';
import ReportsActions from '../pages/reports/actions';
import TopicsActions from '../pages/topics/actions';
import TrashActions from '../pages/trash/actions';
import UsersActions from '../pages/users/actions';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  // 路由切换后的回调
  routeChange: () => {
    // 回到页面顶部
    window.scrollTo(0, 0);

    // 在手机和平板时，切换路由后关闭抽屉导航
    if (window.innerWidth < 1024) {
      (new mdui.Drawer('.mc-drawer')).close();
    }
  },

  answers: AnswersActions,
  articles: ArticlesActions,
  comments: CommentsActions,
  images: ImagesActions,
  options: OptionsActions,
  questions: QuestionsActions,
  reports: ReportsActions,
  topics: TopicsActions,
  trash: TrashActions,
  users: UsersActions,

  components: {

  },
};
