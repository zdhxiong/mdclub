import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import EditorCore from './core';
import './index.less';

let Editor;

export default ({
                  autoSaveKey = false,      // 自动保存的键名
                  onchange = () => {},      // onchange 回调函数
                  onsubmit = () => {},      // 点击发布按钮的回调函数
                  submitting = false,       // 是否正在发布中
                  onClearDrafts = () => {}, // 舍弃草稿后的回调
                  placeholder = '说点什么',  // 编辑器正文 placeholder
                }) => (
  <div
    oncreate={(element) => {
      // 初始化编辑器
      Editor = new EditorCore(
        $(element).find('.content'),
        $(element).find('.toolbar'),
        {
          autoSaveKey,
          onchange,
          onClearDrafts,
          placeholder,
        },
      );

      // 保存编辑器实例到 mc-editor 元素中
      $(element).data('mc-editor-instance', Editor);
    }}
    key="mc-editor"
    class="mc-editor"
  >
    <div class="content mdui-typo"></div>
    <div class="toolbar">
      <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close>
        <i class="mdui-icon material-icons">close</i>
      </button>
      <div class="mdui-toolbar-spacer"></div>
      <button
        type="button"
        class="submit-text mdui-btn mdui-btn-raised mdui-color-indigo mdui-color-theme"
        onclick={() => {
          onsubmit(Editor);
        }}
        disabled={submitting}
      >{submitting ? '发布中…' : '发布'}</button>
      <button
        type="button"
        class="submit-icon mdui-btn mdui-btn-icon mdui-text-color-indigo mdui-text-color-theme"
        onclick={() => {
          onsubmit(Editor);
        }}
        disabled={submitting}
      >
        <i className="mdui-icon material-icons">send</i>
      </button>
    </div>
  </div>
);
