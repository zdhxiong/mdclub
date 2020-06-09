import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

export default ({ topics }) => (
  <div class="mc-topics-bar">
    {topics.map((topic) => (
      <Link
        to={fullPath(`/topics/${topic.topic_id}`)}
        class="mdui-chip mdui-ripple"
        key={topic.topic_id}
      >
        <img class="mdui-chip-icon" src={topic.cover.small} />
        <span class="mdui-chip-title">{topic.name}</span>
      </Link>
    ))}
  </div>
);
