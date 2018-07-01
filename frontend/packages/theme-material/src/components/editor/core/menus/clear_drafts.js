function ClearDrafts(editor) {
  this.editor = editor;
  this.icon = 'delete';
  this.title = '舍弃草稿';
}

ClearDrafts.prototype = {
  constructor: ClearDrafts,

  onclick() {
    const editor = this.editor;

    editor.setHTML('');

    if (editor.options.autoSaveKey) {
      localStorage.removeItem(editor.options.autoSaveKey);
      editor.options.onClearDrafts();
    }
  },
};

export default ClearDrafts;
