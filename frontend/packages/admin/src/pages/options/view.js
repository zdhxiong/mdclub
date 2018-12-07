import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.options;
  const state = global_state.options;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-options"
      class="mdui-container"
    >
      <div class="mdui-subheader">常规</div>
      <div class="mdui-card">

      </div>
      <div class="mdui-subheader">外观</div>
      <div class="mdui-subheader">邮箱</div>
      <div class="mdui-subheader">存储</div>
      <div class="mdui-subheader">缓存</div>
    </div>
  );
};
