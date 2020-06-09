import { h } from 'hyperapp';
import cc from 'classcat';
import $ from 'mdui.jq';
import mdui from 'mdui';
import { emit } from '~/utils/pubsub';
import { fullPath } from '~/utils/path';
import currentUser from '~/utils/currentUser';
import './index.less';

const copy = (url) => {
  let textArea;

  // 判断是不是ios端
  function isOS() {
    return window.navigator.userAgent.match(/ipad|iphone/i);
  }

  // 创建文本元素
  function createTextArea(text) {
    textArea = document.createElement('textArea');
    textArea.value = text;
    document.body.appendChild(textArea);
  }

  // 选择内容
  function selectText() {
    if (isOS()) {
      const range = document.createRange();
      range.selectNodeContents(textArea);

      const selection = window.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
      textArea.setSelectionRange(0, 999999);
    } else {
      textArea.select();
    }
  }

  // 复制到剪贴板
  function copyToClipboard() {
    try {
      if (document.execCommand('Copy')) {
        mdui.snackbar('已复制');
      } else {
        mdui.snackbar('复制失败！请手动复制！');
      }
    } catch (err) {
      mdui.snackbar('复制失败！请手动复制！');
    }

    document.body.removeChild(textArea);
  }

  createTextArea(url);
  selectText();
  copyToClipboard();
};

/**
 * @param type question, answer, article, comment, user, topic
 * @param item
 * @param extraOptions [{ name, onClick }] 额外的菜单项
 */
export default ({ type, item, extraOptions = null }) => {
  let url;
  let title;

  // eslint-disable-next-line default-case
  switch (type) {
    case 'question':
      url = `/questions/${item.question_id}`;
      title = item.title;
      break;

    case 'answer':
      url = `/questions/${item.question_id}/answers/${item.answer_id}`;
      title = item.title;
      break;

    case 'article':
      url = `/articles/${item.article_id}`;
      title = item.title;
      break;

    case 'comment':
      title = item.content;
      break;

    case 'user':
      url = `/users/${item.user_id}`;
      title = item.username;
      break;

    case 'topic':
      url = `/topics/${item.topic_id}`;
      title = item.name;
      break;
  }

  url = `${window.location.protocol}//${window.location.host}${fullPath(url)}`;

  const Item = ({ name, onClick }) => (
    <li class="mdui-menu-item">
      <a class="mdui-ripple" onclick={onClick}>
        {name}
      </a>
    </li>
  );

  return (
    <div
      class="mc-options-button"
      oncreate={(element) => {
        const $element = $(element);
        // eslint-disable-next-line no-new
        new mdui.Menu(
          $element.children('button'),
          $element.children('.mdui-menu'),
          {
            position: 'top',
            align: 'right',
          },
        );
      }}
    >
      <button
        class={cc([
          'mdui-btn',
          'mdui-btn-icon',
          'mdui-text-color-theme-icon',
          'mdui-ripple',
        ])}
      >
        <i class="mdui-icon material-icons">more_vert</i>
      </button>
      <ul class="mdui-menu">
        <If condition={type !== 'comment'}>
          <Item name="复制链接" onClick={() => copy(url)} />
          <Item
            name="分享"
            onClick={() => emit('share_dialog_open', { url, title })}
          />
        </If>
        <If
          condition={
            type !== 'topic' &&
            (type !== 'user' ||
              !currentUser() ||
              currentUser().user_id !== item.user_id)
          }
        >
          <Item
            name="举报"
            onClick={() => emit('report_dialog_open', { type, item })}
          />
        </If>
        <If condition={extraOptions}>
          {extraOptions.map((option) => (
            <Item name={option.name} onClick={option.onClick} />
          ))}
        </If>
      </ul>
    </div>
  );
};
