import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';
import emptyImage from '~/static/image/following_empty.png';

export default ({ show = false, title = '没有数据', description = '' }) => (
  <div
    class={cc(['mc-empty', { 'mdui-hidden': !show }])}
    style={{
      backgroundImage: `url(${window.G_ROOT}/static/theme/material/${emptyImage})`,
    }}
    key="me-empty"
  >
    <div class="title">{title}</div>
    <If condition={description}>
      <div class="description">{description}</div>
    </If>
  </div>
);
