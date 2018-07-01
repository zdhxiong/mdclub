import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.register;
  const state = global_state.components.register;

  return () => (
    <div
      oncreate={element => actions.init({ element, global_actions })}
      key="mc-register"
      class="mc-register mdui-dialog"
    >
      <button
        class={cc([
          'mdui-btn',
          'mdui-btn-icon',
          'mdui-text-color-white',
          state.email_valid ? 'back' : 'close',
        ])}
        onclick={state.email_valid ? actions.prevStep : actions.close}
      >
        <i class="mdui-icon material-icons">{state.email_valid ? 'arrow_back' : 'close'}</i>
      </button>

      {state.email_valid ? <i class="mdui-icon material-icons avatar">account_circle</i> : ''}

      <div class="mdui-dialog-title mdui-color-green mdui-text-color-white">{state.email_valid ? state.email : '创建新账号'}</div>

      {/* 邮箱验证 */}
      <form
        onsubmit={actions.nextStep}
        class={cc([
          {
            'mdui-hidden': state.email_valid,
          },
        ])}
      >
        {/* 邮箱 */}
        <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-floating-label',
            'mdui-textfield-has-bottom',
            {
              'mdui-textfield-invalid': state.email_msg,
              'mdui-textfield-not-empty': state.email,
            },
          ])}
        >
          <label class="mdui-textfield-label">邮箱</label>
          <input
            class="mdui-textfield-input"
            name="email"
            type="email"
            required="required"
            value={state.email}
            oninput={actions.input}
          />
          <div class="mdui-textfield-error">{state.email_msg || '邮箱格式错误'}</div>
        </div>

        {/* 图形验证码 */}
        {state.captcha_token ? <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-floating-label',
            'mdui-textfield-has-bottom',
            'captcha-field',
            {
              'mdui-textfield-invalid': state.captcha_code_msg,
              'mdui-textfield-not-empty': state.captcha_code,
            },
          ])}
          oncreate={actions.showCaptchaLine}
          ondestroy={actions.hideCaptchaLine}
        >
          <label class="mdui-textfield-label">图形验证码</label>
          <input
            class="mdui-textfield-input"
            name="captcha_code"
            type="text"
            required="required"
            value={state.captcha_code}
            oninput={actions.input}
          />
          <div class="mdui-textfield-error">{state.captcha_code_msg || '请输入图形验证码'}</div>
          <img
            class="captcha-image"
            src={state.captcha_image}
            title="点击刷新验证码"
            onclick={actions.captchaRefresh}
          />
        </div> : ''}

        {/* 邮件验证码 */}
        <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-floating-label',
            'mdui-textfield-has-bottom',
            'send-email-field',
            {
              'mdui-textfield-invalid': state.email_code_msg,
              'mdui-textfield-not-empty': state.email_code,
            },
          ])}
        >
          <labe class="mdui-textfield-label">邮件验证码</labe>
          <input
            class="mdui-textfield-input"
            name="email_code"
            type="text"
            required="required"
            value={state.email_code}
            oninput={actions.input}
          />
          <div class="mdui-textfield-error">{state.email_code_msg || '验证码不能为空'}</div>
          <button
            class="mdui-btn send-email"
            type="button"
            disabled={state.sending || state.show_re_send_countdown}
            onclick={actions.sendEmail}
          >
            {
              /* eslint-disable */
              state.show_re_send_countdown ?
                state.re_send_countdown + 's' :
                state.sending ?
                  '发送中…' :
                  '发送验证码'
              /* eslint-enable */
            }
          </button>
        </div>

        {/* submit */}
        <div class="actions">
          <div
            class="mdui-btn mdui-ripple more-option mc-login-trigger"
            onclick={() => {
              actions.close();
              global_actions.components.login.open();
            }}
          >已有账号？</div>
          <button
            type="submit"
            class="mdui-btn mdui-btn-raised mdui-color-theme-accent mdui-float-right"
            disabled={state.verifying}
          >{state.verifying ? '正在验证…' : '下一步'}</button>
        </div>
      </form>

      {/* 注册信息提交 */}
      <form
        class={cc([
          {
            'mdui-hidden': !state.email_valid,
          },
        ])}
        onsubmit={actions.register}
      >
        {/* 用户名 */}
        <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-floating-label',
            'mdui-textfield-has-bottom',
            {
              'mdui-textfield-invalid': state.username_msg,
              'mdui-textfield-not-empty': state.username,
            },
          ])}
        >
          <label class="mdui-textfield-label">用户名</label>
          <input
            class="mdui-textfield-input"
            name="username"
            type="text"
            required="required"
            value={state.username}
            oninput={actions.input}
          />
          <div class="mdui-textfield-error">{state.username_msg || '用户名不能为空'}</div>
        </div>

        {/* 密码 */}
        <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-floating-label',
            'mdui-textfield-has-bottom',
            {
              'mdui-textfield-invalid': state.password_msg,
              'mdui-textfield-not-empty': state.password,
            },
          ])}
        >
          <label class="mdui-textfield-label">密码</label>
          <input
            class="mdui-textfield-input"
            name="password"
            type="password"
            required="required"
            value={state.password}
            oninput={actions.input}
          />
          <div class="mdui-textfield-error">{state.password_msg || '密码不能为空'}</div>
        </div>

        {/* submit */}
        <div class="actions mdui-clearfix">
          <button
            type="submit"
            class="mdui-btn mdui-btn-raised mdui-color-theme-accent mdui-float-right"
            disabled={state.submitting}
          >{state.submitting ? '注册中…' : '注册'}</button>
        </div>
      </form>
    </div>
  );
};
