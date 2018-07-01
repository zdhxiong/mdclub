import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import cc from 'classcat';
import $ from 'mdui.JQ';
import './index.less';

export default ({ topic }) => (global_state, global_actions) => (
  <div
    class="mdui-col"
    key={topic.topic_id}
    oncreate={element => $(element).mutation()}
  >
    <div class="mdui-card item" style={{
      backgroundImage: `url("${topic.cover.s}")`,
    }}>
      <div
        class="mdui-ripple info"
        onclick={() => {
          global_actions.topics.saveScrollPosition();
          location.actions.go($.path(`/topics/${topic.topic_id}`));
        }}
      >
        <div class="name">{topic.name}</div>
        <div class="follower">{topic.follower_count} 人关注</div>
      </div>
      <div class="actions">
        <button
          class={cc([
            'mdui-btn',
            'mdui-text-color-theme',
            {
              following: topic.relationship.is_following,
            },
          ])}
          onclick={() => { global_actions.topics.toggleFollow(topic.topic_id); }}
        >{topic.relationship.is_following ? '已关注' : '关注'}</button>
      </div>
    </div>
  </div>
);
