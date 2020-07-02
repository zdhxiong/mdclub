import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

import Loading from '~/components/loading/view.jsx';

export default ({ state, actions }) => (
  <div
    class="mc-topic-edit mdui-dialog"
    oncreate={(element) => actions.onCreate({ element })}
  >
    <Loading show={state.loading} />

    <If condition={!state.loading}>
      <div class="mdui-dialog-title">
        {state.topic_id ? '编辑话题' : '添加话题'}
      </div>
      <div class="mdui-dialog-content">
        <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-has-bottom',
            { 'mdui-textfield-invalid': state.name_msg },
          ])}
        >
          <label class="mdui-textfield-label">话题名称</label>
          <input
            class="mdui-textfield-input"
            name="name"
            type="text"
            maxlength="20"
            autocomplete="off"
            value={state.name}
            oninput={actions.onInput}
          />
          <div class="mdui-textfield-error">{state.name_msg}</div>
          <div class="mdui-textfield-counter">
            <span class="mdui-textfield-counter-inputed">
              {state.name.length}
            </span>{' '}
            / 20
          </div>
        </div>

        <div
          class={cc([
            'mdui-textfield',
            'mdui-textfield-has-bottom',
            { 'mdui-textfield-invalid': state.description_msg },
          ])}
        >
          <label class="mdui-textfield-label">话题描述</label>
          <textarea
            class="mdui-textfield-input"
            name="description"
            maxlength="1000"
            rows="3"
            autocomplete="off"
            value={state.description}
            oninput={actions.onInput}
          />
          <div class="mdui-textfield-error">{state.description_msg}</div>
          <div class="mdui-textfield-counter">
            <span class="mdui-textfield-counter-inputed">
              {state.description.length}
            </span>{' '}
            / 1000
          </div>
        </div>

        <div class={cc(['cover-wrapper', { invalid: state.cover_msg }])}>
          <div class="title mdui-text-color-theme-secondary">封面图片</div>
          <div class="content">
            <img
              class={cc([{ 'mdui-hidden': !state.cover }])}
              src={state.cover}
            />
            <input
              class="mdui-hidden"
              type="file"
              name="cover"
              onchange={actions.coverSelected}
            />
            <div
              class={cc(['placeholder', { selected: state.cover }])}
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
        <button class="mdui-btn mdui-ripple" mdui-dialog-close>
          取消
        </button>
        <button class="mdui-btn mdui-ripple" onclick={actions.submit}>
          确定
        </button>
      </div>
    </If>
  </div>
);
