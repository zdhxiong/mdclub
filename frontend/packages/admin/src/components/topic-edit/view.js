import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import cc from 'classcat';
import './index.less';

export default () => (global_state, global_actions) => {
  const actions = global_actions.components.topicEdit;
  const state = global_state.components.topicEdit;

  return (
    <div
      class="mdui-dialog mc-topic-edit"
      oncreate={() => actions.init({ global_actions })}
    >
      <div class="mdui-dialog-title">{state.topic_id ? '修改话题' : '添加话题'}</div>
      <div class="mdui-dialog-content">

        <div class={cc([
          'mdui-textfield',
          'mdui-textfield-has-bottom',
          { 'mdui-textfield-invalid': state.name_msg },
        ])}>
          <label class="mdui-textfield-label">话题名称</label>
          <input
            class="mdui-textfield-input"
            name="name"
            type="text"
            maxlength="20"
            autocomplete="off"
            oninput={actions.input}
            value={state.name}
          />
          <div class="mdui-textfield-error">{state.name_msg}</div>
        </div>

        <div class={cc([
          'mdui-textfield',
          'mdui-textfield-has-bottom',
          { 'mdui-textfield-invalid': state.description_msg },
        ])}>
          <label class="mdui-textfield-label">话题描述</label>
          <textarea
            class="mdui-textfield-input"
            name="description"
            maxlength="1000"
            rows="3"
            autocomplete="off"
            oninput={actions.input}
            value={state.description}
          ></textarea>
          <div class="mdui-textfield-error">{state.description_msg}</div>
        </div>

        <div class={cc([
          'cover-wrapper',
          { invalid: state.cover_msg },
        ])}>
          <div class="title">封面图片</div>
          <div class="content">
            <img class={cc([{ 'mdui-hidden': !state.cover }])} src={state.cover}/>
            <input class="mdui-hidden" type="file" name="cover" onchange={actions.coverSelected}/>
            <div
              class={cc([
                'placeholder',
                { selected: state.cover },
              ])}
              title="点击上传封面图片"
              onclick={actions.clickCover}
            >
              <i class="mdui-icon material-icons">photo_camera</i>
            </div>
          </div>
          <div class="error">{state.cover_msg}</div>
        </div>

      </div>
      <div class="mdui-dialog-actions">
        <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
        <button class="mdui-btn mdui-ripple" onclick={actions.submit}>确定</button>
      </div>
    </div>
  );
};
