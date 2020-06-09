import { h } from 'hyperapp';
import Submit from '~/common/account/components/submit.jsx';

const Button = () => (
  <button
    class="mdui-btn mdui-ripple more-option"
    type="button"
    mdui-menu="{target: '#mc-login-menu', position: 'top', covered: true}"
  >
    更多选项
    <i class="mdui-icon material-icons mdui-text-color-theme-icon">
      arrow_drop_down
    </i>
  </button>
);

const Menu = ({ actions }) => (
  <ul class="mdui-menu" id="mc-login-menu">
    <li class="mdui-menu-item">
      <a onclick={actions.toReset} class="mdui-ripple">
        忘记密码
      </a>
    </li>
    <li class="mdui-menu-item">
      <a onclick={actions.toRegister} class="mdui-ripple">
        创建新账号
      </a>
    </li>
  </ul>
);

export default ({ state, actions }) => (
  <div class="actions mdui-clearfix">
    <Button />
    <Menu actions={actions} />
    <Submit
      disabled={state.submitting}
      text={state.submitting ? '登录中…' : '登录'}
    />
  </div>
);
