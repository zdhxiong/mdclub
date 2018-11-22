import { h } from 'hyperapp';
import './index.less';

export default ({ title = '没有数据', description = '' }) => (
  <div class="mc-empty">
    <div class="title">{title}</div>
    <div class="description">{description}</div>
  </div>
);
