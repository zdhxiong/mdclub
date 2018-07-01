import $ from 'mdui.JQ';

/**
 * 验证列表是否被包裹在 <p> 之内，因为可能同时操作多个列表，所以检查所有列表
 * @param $list
 * @param editor
 */
function moveListToRoot($list, editor) {
  $list.each((i, ol) => {
    const $ol = $(ol);
    const $parent = $ol.parent();

    if ($parent.is(editor.$content)) {
      return true;
    }

    editor.selection.createRangeByElem($parent, false, true);
    editor.cmd.do('replaceRoot', ol);

    return true;
  });
}

/**
 * 把纯文本、b、strong、i、em、a 标签包裹的元素移到 p 标签中，移除 br 标签
 * @param editor
 */
function moveElemToP(editor) {
  $(editor.$content[0].childNodes).each((i, curElem) => {
    const nodeType = curElem.nodeType;
    const $curElem = $(curElem);

    if (nodeType === 3) {
      // 纯文本，移动到 p 标签中
      editor.selection.createRangeByElem($curElem.prev(), false, true);
      editor.cmd.do('insertAfterRoot', curElem.nodeValue ? `<p>${curElem.nodeValue}</p>` : '<p><br></p>');
      $curElem.remove();

      return;
    }

    if (nodeType !== 1) {
      // 不是普通 DOM 节点，跳过
      return;
    }

    if (['B', 'STRONG', 'I', 'EM', 'A'].indexOf(curElem.nodeName) > -1) {
      // 移动到 p 标签中
      editor.selection.createRangeByElem($curElem, false, true);
      editor.cmd.do('replaceRoot', curElem.outerHTML ? `<p>${curElem.outerHTML}</p>` : '<p><br></p>');

      return;
    }

    if (curElem.nodeName === 'BR') {
      // 移除 br 元素
      $curElem.remove();
    }
  });
}

export function Ol(editor) {
  this.editor = editor;
  this.icon = 'format_list_numbered';
  this.title = '有序列表';
  this.disable = ['head', 'code', 'image'];
  this._active = false;
}

export function Ul(editor) {
  this.editor = editor;
  this.icon = 'format_list_bulleted';
  this.title = '无序列表';
  this.disable = ['head', 'code', 'image'];
  this._active = false;
}

Ol.prototype = {
  constructor: Ol,

  onclick() {
    const editor = this.editor;

    editor.cmd.do('insertOrderedList');

    moveListToRoot(editor.$content.find('ol'), editor);
    moveElemToP(editor);
  },

  isActive() {
    this._active = !!this.editor.cmd.queryCommandState('insertOrderedList');

    return this._active;
  },
};

Ul.prototype = {
  constructor: Ul,

  onclick() {
    const editor = this.editor;

    editor.cmd.do('insertUnorderedList');

    moveListToRoot(editor.$content.find('ul'), editor);
    moveElemToP(editor);
  },

  isActive() {
    this._active = !!this.editor.cmd.queryCommandState('insertUnOrderedList');

    return this._active;
  },
};
