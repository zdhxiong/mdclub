import { h } from 'hyperapp';
import './index.less';

export default ({ title = '没有数据', description = '' }) => (
  <div class="me-empty" key="me-empty">
    <div class="title">{title}</div>
    {description && <div class="description">{description}</div>}
  </div>
);
