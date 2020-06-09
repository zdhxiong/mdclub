import { h } from 'hyperapp';
import './index.less';

export default ({ topic, onRemove }) => (
  <div class="mdui-chip" key={topic.topic_id}>
    <img class="mdui-chip-icon" src={topic.cover.small} />
    <span class="mdui-chip-title">{topic.name}</span>
    <span class="mdui-chip-delete" onclick={onRemove}>
      <i class="mdui-icon material-icons">cancel</i>
    </span>
  </div>
);
