import { h } from 'hyperapp';
import $ from 'mdui.JQ';
import cc from 'classcat';
import Editor from '../editor';
import './index.less';

export default ({
                  id,
                  autoSaveKey,
                  onchange,
                  onsubmit,
                  submitting,
                  onClearDrafts,
                  withTitle = true,               // 是否包含标题
                  titlePlaceholder = '标题',       // 标题的 placeholder
                  contentPlaceholder = '说点什么',  // 内容的 placeholder
                }) => (
  <div
    class={cc([
      'mdui-dialog',
      'mc-editor-dialog',
      {
        'with-title': withTitle,
      },
    ])}
    id={id}
    key="mc-editor-dialog"
  >
    <input
      type="text"
      class="title"
      placeholder="标题"
      maxlength="50"
      placeholder={titlePlaceholder}
    />
    <Editor
      autoSaveKey={autoSaveKey}
      onchange={onchange}
      onsubmit={onsubmit}
      submitting={submitting}
      onClearDrafts={onClearDrafts}
      placeholder={contentPlaceholder}
    />
  </div>
);
