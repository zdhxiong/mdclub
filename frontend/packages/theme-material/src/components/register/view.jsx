import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

import Close from '~/common/account/components/close.jsx';
import Avatar from '~/common/account/components/avatar.jsx';
import Title from '~/common/account/components/title.jsx';
import FieldEmail from '~/common/account/components/field-email.jsx';
import FieldCaptcha from '~/common/account/components/field-captcha.jsx';
import FieldEmailCode from '~/common/account/components/field-email-code.jsx';
import FieldName from '~/common/account/components/field-name.jsx';
import FieldPassword from '~/common/account/components/field-password.jsx';
import SendActions from './components/send-actions.jsx';
import RegisterActions from './components/register-actions.jsx';

export default ({ state, actions }) => (
  <div
    oncreate={(element) => actions.onCreate({ element })}
    key="register"
    class="mc-account mc-register mdui-dialog"
  >
    <Close
      onClick={state.email_valid ? actions.prevStep : actions.close}
      icon={state.email_valid ? 'arrow_back' : 'close'}
    />
    <If condition={state.email_valid}>
      <Avatar />
    </If>
    <Title text={state.email_valid ? state.email : '创建新账号'} />
    <form
      onsubmit={actions.nextStep}
      class={cc([{ 'mdui-hidden': state.email_valid }])}
      key="send"
    >
      <FieldEmail
        value={state.email}
        message={state.email_msg}
        onInput={actions.onInput}
      />
      <FieldCaptcha
        value={state.captcha_code}
        message={state.captcha_code_msg}
        image={state.captcha_image}
        hide={!state.captcha_token}
        onInput={actions.onInput}
        onRefresh={actions.refreshCaptcha}
      />
      <FieldEmailCode
        value={state.email_code}
        message={state.email_code_msg}
        sending={state.sending}
        show_resend_countdown={state.show_resend_countdown}
        resend_countdown={state.resend_countdown}
        onInput={actions.onInput}
        onSend={actions.sendEmail}
      />
      <SendActions state={state} actions={actions} />
    </form>
    <form
      onsubmit={actions.register}
      class={cc([{ 'mdui-hidden': !state.email_valid }])}
      key="submit"
    >
      <FieldName
        value={state.username}
        message={state.username_msg}
        label="用户名"
        name="username"
        error={state.username_msg || '用户名不能为空'}
        onInput={actions.onInput}
      />
      <FieldPassword
        value={state.password}
        message={state.password_msg}
        label="密码"
        name="password"
        onInput={actions.onInput}
      />
      <RegisterActions state={state} />
    </form>
  </div>
);
