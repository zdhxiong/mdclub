import { h } from 'hyperapp';
import $ from 'mdui.jq';
import mdui from 'mdui';
import { $window } from 'mdui/es/utils/dom';
import { scrollToTop } from '~/utils/scroll';
import './index.less';

import Toolbar from './components/toolbar/view.jsx';
import Tabbar from './components/tabbar/view.jsx';

const onCreate = (element) => {
  const $appbar = $(element).mutation();

  // 移动端自动隐藏工具栏
  const headroom = new mdui.Headroom($appbar, {
    pinnedClass: 'mdui-headroom-pinned-toolbar',
    unpinnedClass: 'mdui-headroom-unpinned-toolbar',
  });

  const updateHeadroom = () => {
    if ($window.width() < 600) {
      headroom.enable();
    } else {
      headroom.disable();
    }
  };

  $window.on('resize', updateHeadroom);
  updateHeadroom();

  // 点击应用栏回到页面顶部
  $appbar.on('click', (e) => {
    if (!window.pageYOffset) {
      return;
    }

    const $target = $(e.target);
    if ($target.is('.mdui-toolbar') || $target.is('mdui-tab')) {
      scrollToTop();
    }
  });
};

export default ({ user, interviewee, notifications }) => (
  <div
    class="mc-appbar mdui-appbar mdui-appbar-fixed"
    oncreate={(element) => onCreate(element)}
  >
    <Toolbar
      user={user}
      interviewee={interviewee}
      notifications={notifications}
    />
    <Tabbar user={user} />
  </div>
);
