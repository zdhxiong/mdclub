import { h } from 'hyperapp';
import cc from 'classcat';
import './dialog.less';

import Comments from './view.jsx';

/**
 * 评论放在模态框中
 */
export default ({ state, actions }) => (
  <div
    class={cc([
      'mc-comments-dialog',
      'mdui-dialog',
      {
        'mdui-dialog-open': state.open_dialog,
      },
    ])}
  >
    <Comments
      commentable_type=""
      commentable_id={0}
      isInDialog={true}
      state={state}
      actions={actions}
    />
  </div>
);
