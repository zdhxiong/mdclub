import { h } from 'hyperapp';
import cc from 'classcat';

const sendText = (sending) => (sending ? '发送中…' : '发送验证码');

export default ({
  value,
  message,
  sending,
  show_resend_countdown,
  resend_countdown,
  onInput,
  onSend,
}) => (
  <div
    class={cc([
      'mdui-textfield',
      'mdui-textfield-floating-label',
      'mdui-textfield-has-bottom',
      'send-email-field',
      {
        'mdui-textfield-invalid': message,
        'mdui-textfield-not-empty': value,
      },
    ])}
  >
    <labe class="mdui-textfield-label">邮件验证码</labe>
    <input
      oninput={onInput}
      value={value}
      class="mdui-textfield-input"
      name="email_code"
      type="text"
      autocomplete="off"
      required="required"
    />
    <div class="mdui-textfield-error">{message || '验证码不能为空'}</div>
    <button
      onclick={onSend}
      class="mdui-btn send-email"
      type="button"
      disabled={sending || show_resend_countdown}
    >
      {show_resend_countdown ? `${resend_countdown}s` : sendText(sending)}
    </button>
  </div>
);
