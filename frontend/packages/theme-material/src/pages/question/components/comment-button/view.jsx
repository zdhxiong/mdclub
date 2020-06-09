import { h } from 'hyperapp';
import './index.less';

import IconButton from '~/components/icon-button/view.jsx';

export default ({ item, onClick }) => (
  <IconButton
    cls="comment"
    icon="comment"
    tooltip="查看评论"
    badge={item.comment_count}
    onClick={onClick}
  />
);
