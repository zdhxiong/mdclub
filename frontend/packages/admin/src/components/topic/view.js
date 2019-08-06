import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../elements/loading';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.topic;
  const state = global_state.components.topic;

  return (
    <div
      class="mdui-dialog mdui-card mc-topic"
      oncreate={() => actions.init({ global_actions })}
    >
      <If condition={!state.loading && state.topic}>
        <div class="mdui-card-media">
          <img src={state.topic.cover.m}/>
          <div class="actions">
            <button
              class="mdui-fab mdui-fab-mini mdui-color-blue-accent"
              mdui-tooltip="{content: '编辑', delay: 300}"
              onclick={actions.toEdit}
            >
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button
              class="mdui-fab mdui-fab-mini mdui-color-pink-accent"
              mdui-tooltip="{content: '删除', delay: 300}"
              onclick={actions.delete}
            >
              <i class="mdui-icon material-icons">delete</i>
            </button>
          </div>
        </div>
        <div class="mdui-card-primary">
          <div class="mdui-card-primary-title">{state.topic.name}</div>
          <If condition={state.topic.delete_time}>
            <div class="mdui-card-primary-subtitle">删除于 {timeHelper.friendly(state.topic.delete_time)}</div>
          </If>
        </div>
        <div class="mdui-card-content">{state.topic.description}</div>
        <div class="mdui-list mdui-list-dense mdui-text-color-theme-secondary">
          <a class="mdui-list-item mdui-ripple">
            <div class="mdui-list-item-content">ID</div>
            <div>{state.topic.topic_id}</div>
          </a>
          <a class="mdui-list-item mdui-ripple">
            <div class="mdui-list-item-content">关注者数量</div>
            <div>{state.topic.follower_count}</div>
          </a>
          <a class="mdui-list-item mdui-ripple">
            <div class="mdui-list-item-content">文章数量</div>
            <div>{state.topic.article_count}</div>
          </a>
          <a class="mdui-list-item mdui-ripple">
            <div class="mdui-list-item-content">提问数量</div>
            <div>{state.topic.question_count}</div>
          </a>
        </div>
      </If>
      <If condition={state.loading}><Loading/></If>
    </div>
  );
};
