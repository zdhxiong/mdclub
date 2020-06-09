import { h } from 'hyperapp';
import './index.less';

import Close from '~/common/account/components/close.jsx';
import Title from '~/common/account/components/title.jsx';
import FieldName from '~/common/account/components/field-name.jsx';
import FieldPassword from '~/common/account/components/field-password.jsx';
import FieldCaptcha from '~/common/account/components/field-captcha.jsx';
import Actions from './components/actions.jsx';

export default ({ state, actions }) => (
  <div
    oncreate={(element) => actions.onCreate({ element })}
    key="login"
    class="mc-account mc-login mdui-dialog"
  >
    <Close onClick={actions.close} icon="close" />
    <Title text="登录" />
    <form onsubmit={actions.login}>
      <FieldName
        value={state.name}
        message={state.name_msg}
        label="用户名或邮箱"
        name="name"
        error={state.name_msg || '账号不能为空'}
        onInput={actions.onInput}
      />
      <FieldPassword
        value={state.password}
        message={state.password_msg}
        label="密码"
        name="password"
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
      <Actions state={state} actions={actions} />
    </form>
  </div>
);
