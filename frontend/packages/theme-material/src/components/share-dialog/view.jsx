import { h } from 'hyperapp';
import './index.less';

import WeiboSvg from '~/svg/weibo.svg';
import FacebookSvg from '~/svg/facebook.svg';
import TwitterSvg from '~/svg/twitter.svg';

const Item = ({ name, svg, onClick }) => (
  <div class="mdui-list-item mdui-ripple" onclick={onClick}>
    <img src={`${window.G_ROOT}/static/theme/material/${svg}`} />
    <div class="mdui-list-item-content">{name}</div>
  </div>
);

export default ({ actions }) => (
  <div
    oncreate={(element) => actions.onCreate({ element })}
    key="share-dialog"
    class="mc-share-dialog mdui-dialog"
  >
    <div class="mdui-dialog-title">
      <span>分享</span>
      <button
        class="mdui-btn mdui-btn-icon mdui-ripple"
        onclick={actions.close}
      >
        <i class="mdui-icon material-icons mdui-text-color-theme-icon">close</i>
      </button>
    </div>
    <div class="mdui-dialog-content">
      <div class="mdui-list">
        <Item name="微博" svg={WeiboSvg} onClick={actions.shareToWeibo} />
        <Item
          name="Facebook"
          svg={FacebookSvg}
          onClick={actions.shareToFacebook}
        />
        <Item
          name="Twitter"
          svg={TwitterSvg}
          onClick={actions.shareToTwitter}
        />
      </div>
    </div>
  </div>
);
