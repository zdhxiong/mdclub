import { h } from 'hyperapp';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import TopicChip from '../topic-chip/view.jsx';

const Title = () => (
  <div class="mdui-dialog-title">
    请选择话题
    <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close>
      <i class="mdui-icon material-icons">close</i>
    </button>
  </div>
);

/**
 * @param state 包含 stateAbstract 中的 editorState 和 topicSelectorState
 * @param actions 包含 actionsAbstract 中的 editorActions 和 topicSelectorActions
 */
export default ({ state, actions }) => {
  const {
    topics_data: data,
    topics_pagination: pagination,
    topics_loading: loading,
    editor_selected_topics: selected,
    editor_selected_topic_ids: selected_ids,
  } = state;

  const isEmpty = !loading && !data.length && pagination;
  const isLoading = loading;
  const isLoaded =
    !loading && pagination && pagination.page === pagination.pages;

  const Selected = () => (
    <If condition={selected.length}>
      <div class="selected" key="selected">
        {selected.map((topic, selectedIndex) => (
          <TopicChip
            topic={topic}
            onRemove={() => {
              actions.topicSelectorRemoveOne(selectedIndex);
            }}
          />
        ))}
      </div>
    </If>
  );

  const Item = ({ topic, dataIndex }) => (
    <label class="mdui-list-item" key={topic.topic_id}>
      <div class="mdui-list-item-avatar">
        <img src={topic.cover.small} />
      </div>
      <div class="mdui-list-item-content">
        <div class="mdui-list-item-title mdui-list-item-one-line">
          {topic.name}
        </div>
        <div class="mdui-list-item-text mdui-list-item-one-line">
          {topic.description}
        </div>
      </div>
      <div class="mdui-checkbox">
        <input
          type="checkbox"
          checked={selected_ids.indexOf(topic.topic_id) > -1}
          onchange={(event) => {
            actions.topicSelectorChange({ event, dataIndex });
          }}
        />
        <i class="mdui-checkbox-icon" />
      </div>
    </label>
  );

  return (
    <div key="topic-selector" class="mdui-dialog mc-topic-selector">
      <Title />
      <div class="mdui-dialog-content">
        <Selected />
        <div class="mdui-list" key="list">
          {data.map((topic, dataIndex) => (
            <Item topic={topic} dataIndex={dataIndex} />
          ))}
        </div>
        <Empty
          show={isEmpty}
          title="管理员未发布任何话题"
          description="待管理员发布话题后，会显示在此处"
        />
        <Loading show={isLoading} />
        <Loaded show={isLoaded} />
      </div>
      <div class="mdui-dialog-actions">
        <button class="mdui-btn mdui-ripple" mdui-dialog-confirm>
          确定
        </button>
      </div>
    </div>
  );
};
