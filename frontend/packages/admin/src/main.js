import { app } from 'hyperapp';
import { location } from '@hyperapp/router';
import { withLogger } from '@hyperapp/logger';
import 'mdn-polyfills/CustomEvent';
import Cookies from 'js-cookie';
import mdui from 'mdui';
import { defaults } from 'mdclub-sdk-js';
import 'mdui/dist/css/mdui.min.css';
import 'highlight.js/styles/github-gist.css';

import './init/index.less';

import actions from './init/actions';
import state from './init/state';
import view from './init/view';

// 控制台安全提示
if (window.top === window && window.console && !('ActiveXObject' in window)) {
  setTimeout(() => {
    window.console.log('%c%s', 'color: red; background: yellow; font-size: 24px;', '警告！');
    window.console.log('%c%s', 'color: black; font-size: 18px;', `使用此控制台可能会让攻击者利用 Self-XSS（自跨站脚本）攻击来冒充你，并窃取你的信息。
请勿输入或粘贴你不明白的代码。`);
  }, 0);
}

// 设置 API 默认参数
defaults.token = Cookies.get('token');
defaults.baseURL = window.G_API;
defaults.error = function () {
  mdui.snackbar('系统发生未知错误');
};

window.main = withLogger(app)(state, actions, view, document.body);
location.subscribe(window.main.location);
