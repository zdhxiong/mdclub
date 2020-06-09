import { h } from 'hyperapp';
import Submit from '~/common/account/components/submit.jsx';

const Button = ({ actions }) => (
  <div class="mdui-btn mdui-ripple more-option" onclick={actions.toLogin}>
    已有账号？
  </div>
);

export default ({ state, actions }) => (
  <div class="actions">
    <Button actions={actions} />
    <Submit
      disabled={state.verifying}
      text={state.verifying ? '正在验证…' : '下一步'}
    />
  </div>
);
