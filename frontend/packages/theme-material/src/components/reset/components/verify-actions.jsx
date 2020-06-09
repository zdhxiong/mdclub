import { h } from 'hyperapp';
import Submit from '~/common/account/components/submit.jsx';

const Button = () => (
  <button
    type="button"
    class="mdui-btn mdui-ripple more-option"
    mdui-menu="{target: '#mc-password-reset-menu', position: 'top', covered: true}"
  >
    更多选项
    <i className="mdui-icon material-icons mdui-text-color-theme-icon">
      arrow_drop_down
    </i>
  </button>
);

const Menu = ({ actions }) => (
  <ul class="mdui-menu" id="mc-password-reset-menu">
    <li class="mdui-menu-item">
      <a onclick={actions.toLogin} class="mdui-ripple">
        登录账号
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
  <div class="actions">
    <Button />
    <Menu actions={actions} />
    <Submit
      disabled={state.verifying}
      text={state.verifying ? '正在验证…' : '下一步'}
    />
  </div>
);
