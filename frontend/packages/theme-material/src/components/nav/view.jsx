import { h } from 'hyperapp';
import { location } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

const back = (path) => {
  if (window.history.state) {
    window.history.back();
  } else {
    location.actions.go(fullPath(path));
  }
};

export default ({ path }) => (
  <div class="mc-nav">
    <button
      class="back mdui-btn mdui-color-theme mdui-ripple"
      onclick={() => back(path)}
    >
      <i class="mdui-icon mdui-icon-left material-icons">arrow_back</i>
      返回
    </button>
  </div>
);
