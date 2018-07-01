import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.login;
  const state = global_state.components.login;

  return () => (
    <div
      oncreate={element => actions.init({ element, global_actions })}
      key="mc-login"
      class="mc-login mdui-dialog"
    >
      <button
        onclick={actions.close}
        class="mdui-btn mdui-btn-icon mdui-text-color-white close"
      >
        <i class="mdui-icon material-icons">close</i>
      </button>
      <div class="mdui-dialog-title mdui-color-indigo">登录</div>
      <form onsubmit={actions.login}>
        {/* 账号 */}
        <div class={cc([
          'mdui-textfield',
          'mdui-textfield-floating-label',
          'mdui-textfield-has-bottom',
          {
            'mdui-textfield-invalid': state.name_msg,
            'mdui-textfield-not-empty': state.name,
          },
        ])}>
          <label class="mdui-textfield-label">用户名或邮箱</label>
          <input
            oninput={actions.input}
            value={state.name}
            class="mdui-textfield-input"
            name="name"
            type="text"
            required="required"
          />
          <div class="mdui-textfield-error">{state.name_msg || '账号不能为空'}</div>
        </div>

        {/* 密码 */}
        <div class={cc([
          'mdui-textfield',
          'mdui-textfield-floating-label',
          'mdui-textfield-has-bottom',
          {
            'mdui-textfield-invalid': state.password_msg,
            'mdui-textfield-not-empty': state.password,
          },
        ])}>
          <label class="mdui-textfield-label">密码</label>
          <input
            oninput={actions.input}
            value={state.password}
            class="mdui-textfield-input"
            name="password"
            type="password"
            required="required"
          />
          <div class="mdui-textfield-error">{state.password_msg || '密码不能为空'}</div>
        </div>

        {/* 验证码 */}
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
          <label class="mdui-textfield-label">验证码</label>
          <input
            class="mdui-textfield-input"
            name="captcha_code"
            type="text"
            required="required"
            value={state.captcha_code}
            oninput={actions.input}
          />
          <div class="mdui-textfield-error">{state.captcha_code_msg || '请输入验证码'}</div>
          <img
            class="captcha-image"
            src={state.captcha_image}
            title="点击刷新验证码"
            onclick={actions.captchaRefresh}
          />
        </div> : ''}

        {/* 底部按钮 */}
        <div class="actions mdui-clearfix">
          <button
            class="mdui-btn mdui-ripple more-option"
            type="button"
            mdui-menu="{target: '#mc-login-menu', position: 'top', covered: true}"
          >更多选项</button>
          <ul class="mdui-menu" id="mc-login-menu">
            <li class="mdui-menu-item">
              <a
                onclick={() => {
                  actions.close();
                  global_actions.components.reset.open();
                }}
                class="mdui-ripple mc-password-reset-trigger"
              >忘记密码</a>
            </li>
            <li class="mdui-menu-item">
              <a
                onclick={() => {
                  actions.close();
                  global_actions.components.register.open();
                }}
                class="mdui-ripple mc-register-trigger"
              >创建新账号</a>
            </li>
          </ul>
          <button
            type="submit"
            class="mdui-btn mdui-btn-raised mdui-color-theme-accent mdui-float-right"
            disabled={state.submitting}
          >{state.submitting ? '登录中…' : '登录'}</button>
        </div>

      </form>
    </div>
  );
};
