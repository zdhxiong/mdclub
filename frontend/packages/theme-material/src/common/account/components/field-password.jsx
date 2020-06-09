import { h } from 'hyperapp';
import cc from 'classcat';

export default ({ value, message, label, name, onInput }) => (
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
    <label class="mdui-textfield-label">{label}</label>
    <input
      oninput={onInput}
      value={value}
      class="mdui-textfield-input"
      name={name}
      type="password"
      required="required"
    />
    <div class="mdui-textfield-error">{message || '密码不能为空'}</div>
  </div>
);
