import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';
import emptyImage from '~/static/image/following_empty.png';

export default ({
  show = false,
  title,
  description = '',
  action = false,
  action_text = '',
}) => (
  <div
    class={cc(['mc-empty', { 'mdui-hidden': !show }])}
    style={{
      backgroundImage: `url(${
        window.G_OPTIONS.site_static_url
          ? window.G_OPTIONS.site_static_url
          : `${window.G_ROOT}/static`
      }/theme/material/${emptyImage})`,
    }}
    key="mc-empty"
  >
    <div class="title">{title}</div>
    <If condition={description}>
      <div class="description">{description}</div>
    </If>
    <If condition={action}>
      <button
        class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme"
        onclick={() => {
          action();
        }}
      >
        {action_text}
      </button>
    </If>
  </div>
);
