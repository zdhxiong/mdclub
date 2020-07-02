import { h } from 'hyperapp';
import $ from 'mdui.jq';
import cc from 'classcat';
import './index.less';

export default ({ show }) => (
  <div key="mc-loading" class={cc(['mc-loading', { 'mdui-hidden': !show }])}>
    <div class="mdui-spinner" oncreate={(element) => $(element).mutation()} />
  </div>
);
