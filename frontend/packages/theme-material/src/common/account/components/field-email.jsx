import { h } from 'hyperapp';
import cc from 'classcat';

export default ({ value, message, onInput }) => (
  <div
    class={cc([
      'mdui-textfield',
      'mdui-textfield-floating-label',
      'mdui-textfield-has-bottom',
      {
        'mdui-textfield-invalid': message,
        'mdui-textfield-not-empty': value,
      },
    ])}
  >
    <label class="mdui-textfield-label">邮箱</label>
    <input
      oninput={onInput}
      value={value}
      class="mdui-textfield-input"
      name="email"
      type="email"
      required="required"
    />
    <div class="mdui-textfield-error">{message || '邮箱格式错误'}</div>
  </div>
);
