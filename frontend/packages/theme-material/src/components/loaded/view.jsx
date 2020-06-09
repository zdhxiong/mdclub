import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

export default ({ show }) => (
  <div class={cc(['mc-loaded', { 'mdui-hidden': !show }])} key="mc-loaded">
    已加载完所有数据
  </div>
);
