import { app } from 'hyperapp';
import { location } from '@hyperapp/router';
import 'mdn-polyfills/CustomEvent';
import '../vendor/mdui/css/mdui.css';
import '../node_modules/highlight.js/styles/github-gist.css';

import './init';
import './init/index.less';

import actions from './init/actions';
import state from './init/state';
import view from './init/view';

document.body.innerHTML = '';

window.main = app(state, actions, view, document.body);
location.subscribe(window.main.location);

// 读取当前登陆用户
const user = window.G_USER;
if (user) {
  window.main.user.setState({ user });
  window.G_USER = null;
}
