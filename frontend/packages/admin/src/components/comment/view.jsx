import { h } from 'hyperapp';
import cc from 'classcat';
import { plainText } from '~/utils/html';
import './index.less';

import Loading from '~/components/loading/view.jsx';

export default ({ state, actions }) => {
  const { loading, comment } = state;

  return (
    <div
      class="mc-comment mdui-dialog"
      oncreate={(element) => actions.onCreate({ element })}
    >
      <Loading show={loading} />

      <If condition={comment}>
        <div class="mdui-dialog-content mdui-text-color-theme-text">
          <div
            class="content mdui-typo"
            oncreate={plainText(comment.content)}
            onupdate={plainText(comment.content)}
          />
          <div class="actions">
            <button
              class="mdui-fab mdui-fab-mini mdui-color-theme"
              mdui-tooltip="{content: '编辑', delay: 300}"
              onclick={actions.toEdit}
            >
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button
              class="delete mdui-fab mdui-fab-mini"
              mdui-tooltip="{content: '删除', delay: 300}"
              onclick={actions.delete}
            >
              <i class="mdui-icon material-icons">delete</i>
            </button>
          </div>
        </div>
      </If>
      <div class={cc(['mdui-dialog-actions', { 'mdui-hidden': !comment }])}>
        <button class="mdui-btn mdui-ripple" mdui-dialog-close>
          关闭
        </button>
      </div>
    </div>
  );
};
