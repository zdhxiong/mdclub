import { h } from 'hyperapp';
import $ from 'mdui.jq';
import mdui from 'mdui';
import { Link } from 'hyperapp-router';
import {
  fullPath,
  isPathArticle,
  isPathArticles,
  isPathInbox,
  isPathIndex,
  isPathNotifications,
  isPathQuestion,
  isPathQuestions,
  isPathTopic,
  isPathTopics,
  isPathUser,
  isPathUsers,
} from '~/utils/path';
import { emit } from '~/utils/pubsub';
import './index.less';

import Avatar from './components/avatar/view.jsx';
import Notification from './components/notification/view.jsx';
import SearchBar from './components/search-bar/view.jsx';
import SearchIcon from './components/search-icon/view.jsx';

const Title = ({ interviewee }) => (
  <div class="title">
    <Choose>
      <When condition={isPathIndex()}>首页</When>
      <When condition={isPathQuestions() || isPathQuestion()}>问答</When>
      <When condition={isPathArticles() || isPathArticle()}>文章</When>
      <When condition={isPathTopics() || isPathTopic()}>话题</When>
      <When condition={isPathUsers()}>人脉</When>
      <When condition={isPathUser() && interviewee}>
        {interviewee.username}
      </When>
      <When condition={isPathInbox()}>私信</When>
      <When condition={isPathNotifications()}>通知</When>
    </Choose>
  </div>
);

const LoginBtn = () => (
  <div class="login mdui-btn mdui-btn-dense" onclick={() => emit('login_open')}>
    登录
  </div>
);

const RegisterBtn = () => (
  <div
    class="register mdui-btn mdui-btn-dense"
    onclick={() => emit('register_open')}
  >
    注册
  </div>
);

export default ({ user, interviewee, notifications }) => (
  <div class="toolbar mdui-toolbar">
    <button
      class="drawer mdui-btn mdui-btn-icon mdui-ripple"
      oncreate={(element) => {
        const $drawer = $('.mc-drawer');
        const drawer = new mdui.Drawer($drawer, { swipe: true });

        $drawer.data('drawerInstance', drawer);
        $(element).on('click', () => drawer.toggle());
      }}
    >
      <i class="mdui-icon material-icons">menu</i>
    </button>
    <Link class="headline" to={fullPath('/')}>
      {window.G_OPTIONS.site_name}
    </Link>
    <Title interviewee={interviewee} />
    <SearchBar />
    <div class="mdui-toolbar-spacer" />
    <SearchIcon />
    <If condition={user}>
      <Notification count={notifications.count} />
      <Avatar user={user} />
    </If>
    <If condition={!user}>
      <LoginBtn />
      <RegisterBtn />
    </If>
  </div>
);
