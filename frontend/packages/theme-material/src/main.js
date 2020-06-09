import '~/initializer/polyfill';
import { app } from 'hyperapp';
import { location } from 'hyperapp-router';
import $ from 'mdui.jq';
import { emit } from '~/utils/pubsub';
import '~/initializer/index';
import actions from '~/initializer/actions';
import state from '~/initializer/state';
import view from '~/initializer/view.jsx';
import listener from '~/initializer/listener';

// import { withLogger } from '@hyperapp/logger';

$(() => {
  document.body.innerHTML = '';

  // window.app = withLogger(app)(state, actions, view, document.body);
  window.app = app(state, actions, view, document.body);

  listener(window.app);
  location.subscribe(window.app.location);

  // 读取当前登陆用户
  const user = window.G_USER;
  if (user) {
    emit('user_update', user);
    window.G_USER = null;
  }
});
