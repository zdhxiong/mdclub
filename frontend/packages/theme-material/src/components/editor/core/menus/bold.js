function Bold(editor) {
  this.editor = editor;
  this.icon = 'format_bold';
  this.title = '粗体';
  this.disable = ['image'];
  this._active = false;
}

Bold.prototype = {
  constructor: Bold,

  onclick() {
    const editor = this.editor;
    const isSeleEmpty = editor.selection.isEmpty();

    if (isSeleEmpty) {
      // 选区是空的，插入并选中一个“空白”
      editor.selection.createEmptyRange('strong');
    }

    // 执行 bold 命令
    editor.cmd.do('bold');

    if (isSeleEmpty) {
      // 需要将选取折叠起来
      editor.selection.collapseRange();
      editor.selection.restore();
    }
  },

  isActive() {
    this._active = !!this.editor.cmd.queryCommandState('bold');

    return this._active;
  },
};

export default Bold;
