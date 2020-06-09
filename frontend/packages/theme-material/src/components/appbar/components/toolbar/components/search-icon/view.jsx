import { h } from 'hyperapp';
import $ from 'mdui.jq';
import './index.less';

const onClick = (e) => {
  const $toolbar = $(e.target).parents('.toolbar');

  $toolbar.addClass('mobile');
  $toolbar.find('.search-bar input')[0].focus();
};

export default () => (
  <div class="search-icon mdui-btn mdui-btn-icon" onclick={onClick}>
    <i class="mdui-icon material-icons mdui-text-color-theme-icon">search</i>
  </div>
);
