import { h } from 'hyperapp';
import { summaryText } from '~/utils/html';
import './index.less';

import Loading from '~/components/loading/view.jsx';

const Item = ({ label, value }) => (
  <a class="mdui-list-item mdui-ripple">
    <div class="mdui-list-item-content">{label}</div>
    <div>{value}</div>
  </a>
);

export default ({ state, actions }) => {
  const { loading, topic } = state;

  return (
    <div class="mc-topic mdui-dialog mdui-card" oncreate={actions.onCreate}>
      <Loading show={loading} />

      <If condition={topic}>
        <div class="mdui-card-media">
          <img src={topic.cover.middle} />
          <div class="actions">
            <button
              class="to-edit mdui-fab mdui-fab-mini"
              mdui-tooltip="{content: '编辑', delay: 300}"
              onclick={actions.toEdit}
            >
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button
              class="delete mdui-fab mdui-fab-mini"
              mdui-tooltip="{content: '删除', delay: 300}"
              onclick={actions.delete}
            >
              <i class="mdui-icon material-icons">delete</i>
            </button>
          </div>
        </div>
        <div class="mdui-card-primary">
          <div
            class="mdui-card-primary-title"
            oncreate={summaryText(topic.name)}
            onupdate={summaryText(topic.name)}
          />
        </div>
        <div
          class="mdui-card-content"
          oncreate={summaryText(topic.description)}
          onupdate={summaryText(topic.description)}
        />
        <div class="mdui-list mdui-list-dense mdui-text-color-theme-secondary">
          <Item label="ID" value={topic.topic_id} />
          <Item label="关注者数量" value={topic.follower_count} />
          <Item label="文章数量" value={topic.article_count} />
          <Item label="提问数量" value={topic.question_count} />
        </div>
      </If>
    </div>
  );
};
