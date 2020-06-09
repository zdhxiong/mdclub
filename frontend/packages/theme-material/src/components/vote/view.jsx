import { h } from 'hyperapp';

import IconButton from '~/components/icon-button/view.jsx';

/**
 * @param actions
 * @param item
 * @param type
 * @param commentIndex 仅 replies 需要
 */
export default ({ actions, item, type, commentIndex = null }) => (
  <div class="mc-vote">
    <IconButton
      icon="thumb_up"
      tooltip={item.relationships.voting === 'up' ? '取消顶' : '顶一下'}
      badge={item.vote_up_count}
      active={item.relationships.voting === 'up'}
      onClick={() => actions.voteUp({ item, type, commentIndex })}
    />
    <IconButton
      icon="thumb_down"
      tooltip={item.relationships.voting === 'down' ? '取消踩' : '踩一下'}
      badge={item.vote_down_count}
      active={item.relationships.voting === 'down'}
      onClick={() => actions.voteDown({ item, type, commentIndex })}
    />
  </div>
);
