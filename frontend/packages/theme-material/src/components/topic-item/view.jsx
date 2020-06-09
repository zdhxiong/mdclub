import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import { emit } from '~/utils/pubsub';
import { summaryText } from '~/utils/html';
import './index.less';

import Follow from '~/components/follow/view.jsx';

export default ({ topic, actions, type = 'topics' }) => (
  <div class="mc-topic-item item-inner" key={topic.topic_id}>
    <div
      class="item mdui-card"
      style={{
        backgroundImage: `url("${topic.cover.small}")`,
      }}
    >
      <Link
        to={fullPath(`/topics/${topic.topic_id}`)}
        class="mdui-ripple info"
        onclick={() => actions.afterItemClick(topic)}
      >
        <div
          class="name mdui-text-color-theme-text"
          oncreate={summaryText(topic.name)}
          onupdate={summaryText(topic.name)}
        />
      </Link>
      <div class="actions">
        <Follow
          item={topic}
          type={type}
          id={topic.topic_id}
          actions={actions}
        />
        <button
          class="followers mdui-btn mdui-ripple mdui-text-color-theme-secondary"
          onclick={() => {
            emit('users_dialog_open', {
              type: 'topic_followers',
              id: topic.topic_id,
            });
          }}
        >
          {topic.follower_count} 人关注
        </button>
      </div>
    </div>
  </div>
);
