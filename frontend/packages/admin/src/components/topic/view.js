import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import './index.less';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.topic;
  const state = global_state.components.topic;

  return (
    <div
      class="mdui-dialog mc-topic"
      oncreate={() => actions.init({ global_actions })}
    >
      <If condition={state.topic}>
        <div class="mdui-dialog-title">
          <span>话题信息</span>
          <div class="actions">
            <button
              class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon"
              mdui-tooltip="{content: '编辑'}"
              onclick={actions.toEdit}
            >
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button
              class="mdui-btn mdui-btn-icon mdui-text-color-theme-icon"
              mdui-tooltip="{content: '删除'}"
              onclick={actions.delete}
            >
              <i class="mdui-icon material-icons">delete</i>
            </button>
          </div>
        </div>
        <div class="mdui-dialog-content">
          <div class="item">
            <label class="label mdui-text-color-theme-secondary">话题名称</label>
            <div class="text mdui-text-color-theme-text">{state.topic.name}</div>
          </div>
          <div class="item">
            <label class="label mdui-text-color-theme-secondary">话题描述</label>
            <div class="text mdui-text-color-theme-text">{state.topic.description}</div>
          </div>
          <div class="item">
            <label class="label mdui-text-color-theme-secondary">封面图片</label>
            <div class="cover">
              <img src={state.topic.cover.s}/>
            </div>
          </div>
        </div>
      </If>
    </div>
  );
};
