import { h } from 'hyperapp';
import { emit } from '~/utils/pubsub';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Follow from '~/components/follow/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';
import { summaryText } from '~/utils/html';

export default ({ topic, loading, actions }) => (
  <div class="mdui-card mdui-card-shadow topic">
    <If condition={topic}>
      <div class="info">
        <div
          class="cover"
          style={{
            backgroundImage: `url("${topic.cover.small}")`,
          }}
        />
        <div class="main">
          <div
            class="name"
            oncreate={summaryText(topic.name)}
            onupdate={summaryText(topic.name)}
          />
          <div class="meta mdui-text-color-theme-secondary">
            <span>{topic.question_count} 个提问</span>
            <span>{topic.article_count} 篇文章</span>
          </div>
          <div
            class="description mdui-text-color-theme-secondary"
            oncreate={summaryText(topic.description)}
            onupdate={summaryText(topic.description)}
          />
        </div>
      </div>
      <div class="actions">
        <Follow item={topic} type="topic" actions={actions} />
        <div class="flex-grow" />
        <OptionsButton
          type="topic"
          item={topic}
          extraOptions={[
            {
              name: `查看 ${topic.follower_count} 位关注者`,
              onClick: () => {
                emit('users_dialog_open', {
                  type: 'topic_followers',
                  id: topic.topic_id,
                });
              },
            },
          ]}
        />
      </div>
    </If>
    <Loading show={loading} />
  </div>
);
