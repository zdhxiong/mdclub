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
      class="mdui-dialog mc-topic"
      oncreate={() => actions.init({ global_actions })}
    >
      <If condition={!state.loading && state.topic}>
        <div class="header" style={{ backgroundImage: `url(${state.topic.cover.m})` }}>
          <div class="gradient"></div>
          <div class="name">{state.topic.name}</div>
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
        <div class="content">
          <If condition={state.topic.delete_time}>
            <div class="item item-delete mdui-text-color-theme-secondary">删除于：{timeHelper.format(state.topic.delete_time)}</div>
          </If>
          <div class="item mdui-text-color-theme-text">{state.topic.description}</div>
          <div class="item chip-wrapper mdui-text-color-theme-text">
            <div class="mdui-chip">
              <span class="mdui-chip-title">话题ID：{state.topic.topic_id}</span>
            </div>
            <div class="mdui-chip">
              <span class="mdui-chip-title">关注者数量：{state.topic.follower_count}</span>
            </div>
            <div class="mdui-chip">
              <span class="mdui-chip-title">文章数量：{state.topic.article_count}</span>
            </div>
            <div class="mdui-chip">
              <span class="mdui-chip-title">提问数量：{state.topic.question_count}</span>
            </div>
          </div>
        </div>
      </If>
      <If condition={state.loading}><Loading/></If>
    </div>
  );
};
