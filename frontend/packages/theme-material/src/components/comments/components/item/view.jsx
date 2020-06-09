import { h } from 'hyperapp';
import cc from 'classcat';
import $ from 'mdui.jq';
import { plainText } from '~/utils/html';
import './index.less';

import UserLine from '~/components/user-line/view.jsx';
import Vote from '~/components/vote/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';
import IconButton from '~/components/icon-button/view.jsx';
import Loading from '~/components/loading/view.jsx';
import NewComment from '../new-comment/view.jsx';
import Reply from '../reply/view.jsx';

export default ({ comment, commentIndex, actions }) => {
  const isRepliesLoaded =
    !comment.replies_loading &&
    comment.replies_pagination &&
    comment.replies_pagination.page === comment.replies_pagination.pages;
  const isRepliesLoading = comment.replies_loading;

  return (
    <div
      class="item"
      key={comment.comment_id}
      oncreate={(element) => $(element).mutation()}
    >
      <UserLine
        actions={actions}
        user={comment.relationships.user}
        time={comment.create_time}
        dataName="comments_data"
        primaryKey="comment_id"
        primaryValue={comment.comment_id}
      />
      <div
        class="content mdui-typo"
        oncreate={plainText(comment.content)}
        onupdate={plainText(comment.content)}
      />
      <div class="actions">
        <Vote actions={actions} item={comment} type="comments" />
        <IconButton
          icon="reply"
          tooltip="发表回复"
          onClick={() => {
            actions.toggleState({
              comment_id: comment.comment_id,
              fieldName: 'show_new_reply',
            });
          }}
        />
        <div class="flex-grow" />
        <OptionsButton type="comment" item={comment} />
      </div>
      <If condition={comment.show_new_reply}>
        <NewComment
          placeholder="添加回复..."
          submitting={comment.reply_submitting}
          onCreate={(element) => {
            setTimeout(() => $(element).find('textarea')[0].focus());
          }}
          onSubmit={({ target }) => {
            actions.submitReply({ target, comment });
          }}
        />
      </If>
      <If condition={comment.reply_count}>
        <div
          class={cc(['reply_count', { 'show-replies': comment.show_replies }])}
          onclick={() => {
            actions.toggleState({
              comment_id: comment.comment_id,
              fieldName: 'show_replies',
            });
          }}
        >
          <i class="mdui-icon material-icons">arrow_drop_down</i>
          <span>
            {comment.show_replies ? '隐藏' : '查看'} {comment.reply_count}{' '}
            条回复
          </span>
        </div>
      </If>
      <If condition={comment.show_replies}>
        <div class="replies">
          {comment.replies_data.map((reply) => (
            <Reply
              comment={reply}
              commentIndex={commentIndex}
              actions={actions}
            />
          ))}
          <Loading show={comment.replies_loading} />
          <div
            class={cc([
              'reply_more',
              { 'mdui-hidden': isRepliesLoaded || isRepliesLoading },
            ])}
            onclick={() => {
              actions.loadReplies({ comment });
            }}
          >
            <i class="mdui-icon material-icons">subdirectory_arrow_right</i>
            <span>显示更多回复</span>
          </div>
        </div>
      </If>
    </div>
  );
};
