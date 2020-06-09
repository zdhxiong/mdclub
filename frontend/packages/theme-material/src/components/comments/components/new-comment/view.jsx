import { h } from 'hyperapp';
import './index.less';

export default ({
  submitting,
  onSubmit,
  onCreate = null,
  placeholder = '写下你的评论...',
}) => (
  <div class="mdui-textfield new-comment" key="new-comment" oncreate={onCreate}>
    <textarea
      class="mdui-textfield-input"
      placeholder={placeholder}
      disabled={submitting}
    />
    <button
      class="submit mdui-btn mdui-btn-raised mdui-color-theme"
      onclick={onSubmit}
      disabled={submitting}
    >
      {submitting ? '发布中' : '发布'}
    </button>
  </div>
);
