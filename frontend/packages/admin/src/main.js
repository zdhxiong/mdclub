import '~/initializer/polyfill';
import { app } from 'hyperapp';
import { location } from 'hyperapp-router';
import $ from 'mdui.jq';
import '~/initializer/index';
import actions from '~/initializer/actions';
import state from '~/initializer/state';
import view from '~/initializer/view.jsx';
import listener from '~/initializer/listener';

// import { withLogger } from '@hyperapp/logger';

$(() => {
  // window.app = withLogger(app)(state, actions, view, document.body);
  window.app = app(state, actions, view, document.body);

  listener(window.app);
  location.subscribe(window.app.location);
});
