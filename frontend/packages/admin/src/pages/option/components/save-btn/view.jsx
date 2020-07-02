import { h } from 'hyperapp';

export default ({ submitting }) => (
  <button
    class="submit mdui-btn mdui-btn-raised mdui-color-theme"
    disabled={submitting}
  >
    {submitting ? '保存中…' : '保存'}
  </button>
);
