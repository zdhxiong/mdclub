import { h } from 'hyperapp';
import $ from 'mdui.jq';
import { richText } from '~/utils/html';
import { emit } from '~/utils/pubsub';
import './index.less';

import UserLine from '~/components/user-line/view.jsx';
import Vote from '~/components/vote/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';
import CommentButton from '../comment-button/view.jsx';

export default ({ answer, actions }) => (
  <div
    class="item"
    key={answer.answer_id}
    oncreate={(element) => $(element).mutation()}
  >
    <UserLine
      actions={actions}
      user={answer.relationships.user}
      time={answer.create_time}
      dataName="answer_data"
      primaryKey="answer_id"
      primaryValue={answer.answer_id}
    />
    <div
      class="content mdui-typo"
      oncreate={richText(answer.content_rendered)}
      onupdate={richText(answer.content_rendered)}
    />
    <div class="actions">
      <Vote actions={actions} item={answer} type="answers" />
      <CommentButton
        item={answer}
        onClick={() => {
          emit('comments_dialog_open', {
            commentable_type: 'answer',
            commentable_id: answer.answer_id,
          });
        }}
      />
      <div class="flex-grow" />
      <OptionsButton type="answer" item={answer} />
    </div>
  </div>
);
