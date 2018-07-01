import { correctUrl } from '../helper/utils';

function Link(editor) {
  this.editor = editor;
  this.icon = 'link';
  this.title = '插入链接';
  this.disable = ['image'];
}

Link.prototype = {
  constructor: Link,

  onclick() {
    const editor = this.editor;

    const $curElem = editor.selection.getContainerElem();
    let defaultUrl = '';

    if ($curElem.is('a')) {
      // 当前选区为 a 元素，则选中整个 a 元素
      editor.selection.createRangeByElem($curElem, null, true);
      defaultUrl = $curElem.attr('href');
    }

    /* eslint-disable no-alert */
    const url = window.prompt('请输入链接地址', defaultUrl);
    if (url) {
      // 链接不为空，添加链接
      editor.cmd.do('createLink', correctUrl(url));
    } else {
      // 链接为空，移除链接
      editor.cmd.do('unlink');
    }
  },

  isActive() {
    this._active = this.editor.selection.getContainerElem().is('a');

    return this._active;
  },
};

export default Link;
