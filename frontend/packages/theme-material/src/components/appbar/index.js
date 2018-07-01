import { h } from 'hyperapp';
import cc from 'classcat';
import mdui, { JQ as $ } from 'mdui';
import { Link } from '@hyperapp/router';
import jump from 'jump.js';
import './index.less';

import AppbarUser from '../appbar-user';

/**
 * 初始化
 */
const init = (element) => {
  const $appbar = $(element);
  const $window = $(window);

  $appbar.mutation();

  // 移动端自动隐藏工具栏
  {
    const headroom = new mdui.Headroom($appbar, {
      pinnedClass: 'mdui-headroom-pinned-toolbar',
      unpinnedClass: 'mdui-headroom-unpinned-toolbar',
    });
    const handle = () => {
      if ($window.width() < 600) {
        headroom.enable();
      } else {
        headroom.disable();
      }
    };

    $window.on('resize', handle);
    handle();
  }


  // 点击应用栏回到页面顶部
  $appbar.on('click', (e) => {
    if (!window.pageYOffset) {
      return;
    }

    const $target = $(e.target);

    if ($target.is('.mdui-toolbar') || $target.is('.mdui-tab')) {
      jump(document.body, {
        duration: 300,
      });
    }
  });
};

/**
 * Tab 选项卡的选项
 */
const TabItem = ({ hash, title, type }) => state => (
  <a href={`#${hash}`} class={cc([
    $.rippleClass(state.theme.primary),
    {
      'mdui-tab-active': state[type].current_tab === hash,
    },
  ])}>{title}</a>
);

/**
 * 登录按钮
 */
const LoginBtn = () => (global_state, global_actions) => (
  <div
    class={cc([
      'mc-login-btn',
      'mdui-btn',
      'mdui-btn-dense',
      $.rippleClass(global_state.theme.primary),
    ])}
    onclick={global_actions.components.login.open}
  >登录</div>
);

/**
 * 注册按钮
 */
const RegisterBtn = () => (global_state, global_actions) => (
  <div
    class={cc([
      'mc-register-btn',
      'mdui-btn',
      'mdui-btn-dense',
      $.rippleClass(global_state.theme.primary),
    ])}
    onclick={global_actions.components.register.open}
  >注册</div>
);

export default () => (global_state, global_actions) => {
  const paths = {
    [$.path('/')]: '首页',
    [$.path('/questions')]: '问答',
    [$.path('/question')]: '问答',
    [$.path('/articles')]: '文章',
    [$.path('/article')]: '文章',
    [$.path('/topics')]: '话题',
    [$.path('/topic')]: '话题',
    [$.path(`/users/${global_state.user.user.user_id}`)]: global_state.user.user.username,
    [$.path('/users')]: '人脉',
    [$.path('/notifications')]: '通知',
    [$.path('/inbox')]: '私信',
  };

  return () => (
    <div
      class="mdui-appbar mdui-appbar-fixed"
      oncreate={element => init(element)}
    >
      {/* 工具栏 */}
      <div class="mdui-toolbar mdui-color-white mdui-color-theme">
        <button
          mdui-drawer="{target: '.mc-drawer', swipe: true}"
          class={cc([
            'mdui-btn',
            'mdui-btn-icon',
            $.rippleClass(global_state.theme.primary),
          ])}
        >
          <i class="mdui-icon material-icons">menu</i>
        </button>
        <Link to={$.path('/')} class="mdui-typo-headline">MDClub</Link>
        <div class="mdui-typo-title mdui-hidden-xs-down">
          {Object.keys(paths).map(path => ($.isPathMatched(path) ? paths[path] : false))}
        </div>
        <div class="mdui-toolbar-spacer"></div>
        {global_state.user.user.user_id ? <AppbarUser/> : ''}
        {!global_state.user.user.user_id ? <LoginBtn/> : ''}
        {!global_state.user.user.user_id ? <RegisterBtn/> : ''}
      </div>

    {/* Tab 选项卡 */}

    {$.isPathMatched('/questions') ?
      <div class="mdui-tab mdui-tab-centered mdui-color-theme" id="tab-questions">
        <TabItem hash="recent" title="最新" type="questions"/>
        <TabItem hash="popular" title="近期热门" type="questions"/>
        {global_state.user.user.user_id ? <TabItem hash="following" title="已关注" type="questions"/> : ''}
      </div> : ''
    }

    {$.isPathMatched('/articles') ?
      <div class="mdui-tab mdui-tab-centered mdui-color-theme" id="tab-articles">
        <TabItem hash="recent" title="最新" type="articles"/>
        <TabItem hash="popular" title="近期热门" type="articles"/>
        {global_state.user.user.user_id ? <TabItem hash="following" title="已关注" type="articles"/> : ''}
      </div> : ''
    }

    {$.isPathMatched('/topics') && global_state.user.user.user_id ?
      <div class="mdui-tab mdui-tab-centered mdui-color-theme" id="tab-topics">
        <TabItem hash="following" title="已关注" type="topics"/>
        <TabItem hash="recommended" title="精选" type="topics"/>
      </div> : ''
    }

    {$.isPathMatched('/users') && global_state.user.user.user_id ?
      <div class="mdui-tab mdui-tab-centered mdui-color-white" id="tab-users">
        <TabItem hash="following" title="已关注" type="users"/>
        <TabItem hash="followers" title="关注者" type="users"/>
        <TabItem hash="recommended" title="找人" type="users"/>
      </div> : ''
    }
  </div>);
};
