import { h } from 'hyperapp';

export default ({ disabled, text }) => (
  <button
    type="submit"
    class="mdui-btn mdui-btn-raised mdui-color-theme action-btn"
    disabled={disabled}
  >
    {text}
  </button>
);
