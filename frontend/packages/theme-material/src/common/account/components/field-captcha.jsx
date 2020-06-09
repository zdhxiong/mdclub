import { h } from 'hyperapp';
import cc from 'classcat';

export default ({ value, message, image, hide, onInput, onRefresh }) => (
  <div
    class={cc([
      'mdui-textfield',
      'mdui-textfield-floating-label',
      'mdui-textfield-has-bottom',
      'captcha-field',
      {
        'mdui-textfield-invalid': message,
        'mdui-textfield-not-empty': value,
        'mdui-hidden': hide,
      },
    ])}
  >
    <label class="mdui-textfield-label">图形验证码</label>
    <input
      oninput={onInput}
      value={value}
      class="mdui-textfield-input"
      name="captcha_code"
      type="text"
      autocomplete="off"
      required={!hide}
    />
    <div class="mdui-textfield-error">{message || '请输入图形验证码'}</div>
    <img
      class="captcha-image"
      src={image}
      title="点击刷新验证码"
      onclick={onRefresh}
    />
  </div>
);
