import $ from 'mdui.JQ';

/**
 * 构造函数
 * @param editor
 * @constructor
 */
function Command(editor) {
  this.editor = editor;
}

Command.prototype = {
  constructor: Command,

  /**
   * 执行命令
   * @param name
   * @param value
   */
  do(name, value) {
    const editor = this.editor;

    // 如果无选取，忽略
    if (!editor.selection.getRange()) {
      return;
    }

    // 恢复选区
    editor.selection.restore();

    // 执行
    const _name = `_${name}`;
    if (this[_name]) {
      // 有自定义事件
      this[_name](value);
    } else {
      // 默认 command
      this._execCommand(name, value);
    }

    // 修改菜单状态
    editor.menus.changeStatus();

    // 最后，恢复选取保证光标在原来的位置闪烁
    editor.selection.saveRange();
    editor.selection.restore();

    // 触发 onchange
    if (editor.change) {
      editor.change();
    }
  },

  /**
   * 自定义 insertHTML 事件，在当前选区中插入指定 HTML
   * @param html
   * @private
   */
  _insertHTML(html) {
    const editor = this.editor;
    const range = editor.selection.getRange();

    if (this.queryCommandSupported('insertHTML')) {
      // W3C
      this._execCommand('insertHTML', html);
    } else if (range.insertNode) {
      // IE
      range.deleteContents();
      range.insertNode($(html)[0]);
    } else if (range.pasteHTML) {
      // IE <= 10
      range.pasteHTML(html);
    }
  },

  /**
   * 用指定 HTML 替换当前选区的 root 元素
   * @param html
   * @private
   */
  _replaceRoot(html) {
    const editor = this.editor;

    const $oldElem = editor.selection.getRootElem();
    const $newElem = $(html).insertAfter($oldElem);
    $oldElem.remove();
    editor.selection.createRangeByElem($newElem, false, true);
    editor.selection.restore();
  },

  /**
   * 在当前选区的 root 元素后面插入指定 html
   * @param html
   * @private
   */
  _insertAfterRoot(html) {
    const editor = this.editor;

    const $oldElem = editor.selection.getRootElem();
    const $newElem = $(html).insertAfter($oldElem);
    editor.selection.createRangeByElem($newElem, false, true);
    editor.selection.restore();
  },

  /**
   * 在当前 $content 的最后追加 html
   * @param html
   * @private
   */
  _appendHTML(html) {
    const editor = this.editor;

    const $newElem = $(html).appendTo(editor.$content);
    editor.selection.createRangeByElem($newElem, false, true);
    editor.selection.restore();
  },

  /**
   * 插入 elem
   * @param $elem
   * @private
   */
  _insertElem($elem) {
    const editor = this.editor;
    const range = editor.selection.getRange();

    if (range.insertNode) {
      range.deleteContents();
      range.insertNode($elem[0]);
    }
  },

  /**
   * 封装 execCommand
   * @param name
   * @param value
   * @private
   */
  _execCommand(name, value) {
    document.execCommand(name, false, value);
  },

  /**
   * 封装 document.queryCommandValue
   * @param name
   * @returns {string}
   */
  queryCommandValue(name) {
    return document.queryCommandValue(name);
  },

  /**
   * 封装 document.queryCommandState
   * @param name
   * @returns {boolean}
   */
  queryCommandState(name) {
    return document.queryCommandState(name);
  },

  /**
   * 封装 document.queryCommandSupported
   * @param name
   * @returns {boolean}
   */
  queryCommandSupported(name) {
    return document.queryCommandSupported(name);
  },
};

export default Command;
