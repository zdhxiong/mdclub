import { h } from 'hyperapp';
import './index.less';

import IconButton from '~/components/icon-button/view.jsx';

/**
 * @param item 用户、提问、文章信息
 * @param type question, topic, topics, user, users, article, relationships-user
 * @param primaryKey 仅 relationships-user 需要传入该参数，表示提问ID或文章ID或评论ID的字段名
 * @param id 若 type 为 topics、users、relationships-user，则需要传入该参数
 * @param dataName 若 type 为 relationships-user 时，需要传入改参数
 * @param actions
 */
export default ({
  item,
  type,
  primaryKey = null,
  id = null,
  dataName = null,
  actions,
}) => {
  const { is_following } = item.relationships;

  return (
    <IconButton
      cls="mc-follow"
      icon="star_border"
      iconActive="star"
      tooltip={is_following ? '取消关注' : '关注'}
      active={is_following}
      onClick={() => actions.toggleFollow({ type, dataName, primaryKey, id })}
    />
  );
};
