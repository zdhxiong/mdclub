import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

export default ({ show = false, title, description, action = false, action_text = '' }) => (
  <div class={cc([
    'mc-empty',
    {
      'mdui-hidden': !show,
    },
  ])}>
    <div class="title">{title}</div>
    <div class="description">{description}</div>
    {action ? <button
      class="mdui-btn mdui-btn-raised mdui-color-indigo mdui-color-theme mdui-ripple"
      onclick={() => { action(); }}
    >{action_text}</button> : ''}
  </div>
);
