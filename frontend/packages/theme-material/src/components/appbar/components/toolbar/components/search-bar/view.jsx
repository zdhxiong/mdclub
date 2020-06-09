import { h } from 'hyperapp';
import $ from 'mdui.jq';
import './index.less';

let $searchbar;
let $input;

const onCreate = (element) => {
  $searchbar = $(element);
  $input = $searchbar.find('input');
};

const onFocus = () => {
  $searchbar.addClass('focus');
};

const onBlur = () => {
  $searchbar.removeClass('focus');
};

const onInput = (e) => {
  if (e.target.value) {
    $searchbar.addClass('not-empty');
  } else {
    $searchbar.removeClass('not-empty');
  }
};

const clearInput = () => {
  $input.val('').trigger('input');
};

const onBack = (e) => {
  $(e.target).parents('.toolbar').removeClass('mobile');
};

const getFormAction = (keyword) => {
  const query = `site:${window.location.host}%20${keyword}`;

  if (window.G_OPTIONS.search_type === 'third') {
    switch (window.G_OPTIONS.search_third) {
      case 'google':
        return `https://www.google.com/search?q=${query}`;
      case 'bing':
        return `https://www.bing.com/search?q=${query}`;
      case 'baidu':
        return `https://www.baidu.com/s?ws=${query}`;
      case 'sogou':
        return `https://www.sogou.com/web?query=${query}`;
      case '360':
        return `https://www.so.com/s?q=${query}`;
      default:
        return '';
    }
  }

  return '';
};

const onSubmit = (e) => {
  e.preventDefault();

  const value = $input.val();

  if (!value) {
    return;
  }

  const ua = window.navigator.userAgent;

  if (
    ua.indexOf('iPad') > -1 ||
    ua.indexOf('Ipod') > -1 ||
    ua.indexOf('iPhone') > -1
  ) {
    window.location.href = getFormAction(value);
  } else {
    window.open(getFormAction(value), '_blank');
  }
};

export default () => (
  <form
    method="get"
    class="search-bar"
    oncreate={(element) => onCreate(element)}
    onsubmit={onSubmit}
  >
    <button type="button" class="back mdui-btn mdui-btn-icon" onclick={onBack}>
      <i class="mdui-icon material-icons mdui-text-color-theme-icon">
        arrow_back
      </i>
    </button>
    <button type="submit" class="submit mdui-btn mdui-btn-icon">
      <i class="mdui-icon material-icons mdui-text-color-theme-icon">search</i>
    </button>
    <input
      type="text"
      placeholder="搜索文章与问答"
      onfocus={onFocus}
      onblur={onBlur}
      oninput={onInput}
    />
    <button
      type="button"
      class="cancel mdui-btn mdui-btn-icon"
      onclick={clearInput}
    >
      <i class="mdui-icon material-icons mdui-text-color-theme-icon">close</i>
    </button>
  </form>
);
