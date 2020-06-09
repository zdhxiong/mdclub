import { h } from 'hyperapp';
import $ from 'mdui.jq';
import { plainText } from '~/utils/html';
import './index.less';

import UserLine from '~/components/user-line/view.jsx';
import Vote from '~/components/vote/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';

export default ({ comment, commentIndex, actions }) => (
  <div
    class="item"
    key={comment.comment_id}
    oncreate={(element) => $(element).mutation()}
  >
    <UserLine
      actions={actions}
      user={comment.relationships.user}
      time={comment.create_time}
      dataName={`comments_data[${commentIndex}].replies_data`}
      primaryKey="comment_id"
      primaryValue={comment.comment_id}
    />
    <div
      class="content mdui-typo"
      oncreate={plainText(comment.content)}
      onupdate={plainText(comment.content)}
    />
    <div class="actions">
      <Vote
        actions={actions}
        item={comment}
        commentIndex={commentIndex}
        type="replies"
      />
      <div class="flex-grow" />
      <OptionsButton type="comment" item={comment} />
    </div>
  </div>
);
