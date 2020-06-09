import { h } from 'hyperapp';
import cc from 'classcat';

export default ({ onClick, icon }) => (
  <button onclick={onClick} class={cc(['mdui-btn', 'mdui-btn-icon', icon])}>
    <i class="mdui-icon material-icons">{icon}</i>
  </button>
);
