import { h } from 'hyperapp';
import mdui from 'mdui';
import './page.less';

import Comments from './view.jsx';
import NewComment from './components/new-comment/view.jsx';

/**
 * 评论直接放在页面中
 * 用于文章的评论
 * @param commentable_type
 * @param commentable_id
 */
export default ({ commentable_type, commentable_id }) => (
  globalState,
  globalActions,
) => {
  const state = globalState.comments;
  const actions = globalActions.comments;

  return (
    <div class="mc-comments-page">
      <Comments
        commentable_type={commentable_type}
        commentable_id={commentable_id}
        isInDialog={false}
        state={state}
        actions={actions}
      />
      <div
        class="new-comment-fixed"
        oncreate={(element) => {
          // eslint-disable-next-line no-new
          new mdui.Headroom(element, {
            pinnedClass: 'mdui-headroom-pinned-down',
            unpinnedClass: 'mdui-headroom-unpinned-down',
          });
        }}
      >
        <div class="mdui-container">
          <NewComment
            submitting={state.submitting}
            onSubmit={actions.onSubmit}
          />
        </div>
      </div>
    </div>
  );
};
