function Italic(editor) {
  this.editor = editor;
  this.icon = 'format_italic';
  this.title = '斜体';
  this.disable = ['image'];
  this._active = false;
}

Italic.prototype = {
  constructor: Italic,

  onclick() {
    const editor = this.editor;
    const isSeleEmpty = editor.selection.isEmpty();

    if (isSeleEmpty) {
      // 选区是空的，插入并选中一个“空白”
      editor.selection.createEmptyRange('em');
    }

    // 执行 italic 命令
    editor.cmd.do('italic');

    if (isSeleEmpty) {
      // 需要将选取折叠起来
      editor.selection.collapseRange();
      editor.selection.restore();
    }
  },

  isActive() {
    this._active = !!this.editor.cmd.queryCommandState('italic');

    return this._active;
  },
};

export default Italic;
