import { h } from 'hyperapp';
import cc from 'classcat';
import $ from 'mdui.jq';
import { COMMON_IMAGE_UPLOAD_FAILED } from 'mdclub-sdk-js/es/errors';
import Editor from 'mdui.editor/es/index';
import './index.less';

import TopicChip from './components/topic-chip/view.jsx';

const HeaderIcon = ({ iconTitle, icon, onClick }) => (
  <i class="mdui-icon material-icons" title={iconTitle} onclick={onClick}>
    {icon}
  </i>
);

export default ({
  id, // 唯一ID
  title, // 头部显示的名称
  withTitle = false, // 是否包含标题
  withTopics = false, // 是否包含话题选择
  onSubmit, // 点提交按钮的函数
  publishing, // 是否正在提交内容
  state, // 包含 stateAbstract 中的 editorState 和 topicSelectorState
  actions, // 包含 actionsAbstract 中的 editorActions 和 topicSelectorActions
}) => {
  const Header = () => (
    <div class="header">
      <div class="header-title">{title}</div>
      <div class="header-actions">
        <HeaderIcon
          iconTitle="保存为草稿并关闭"
          icon="close"
          onClick={actions.editorClose}
        />
      </div>
    </div>
  );

  const Title = () => (
    <input
      class="editor-title mdui-text-color-theme-text"
      placeholder="请输入标题"
    />
  );

  const Topics = () => (
    <div class="editor-topics">
      <If condition={state.editor_selected_topics.length}>
        <div class="chip-wrapper">
          {state.editor_selected_topics.map((topic, selectedIndex) => (
            <TopicChip
              topic={topic}
              onRemove={() => {
                actions.topicSelectorRemoveOne(selectedIndex);
              }}
            />
          ))}
        </div>
      </If>
      <If condition={!state.editor_selected_topics.length}>
        <span
          class="placeholder"
          title="点击添加话题"
          onclick={() => {
            actions.topicSelectorOpen(id);
          }}
        >
          请至少选择 1 个话题
        </span>
      </If>
      <button
        class="add mdui-btn mdui-btn-icon mdui-btn-dense mdui-ripple"
        title="点击添加话题"
        onclick={() => {
          actions.topicSelectorOpen(id);
        }}
      >
        <i class="mdui-icon material-icons mdui-text-color-theme-icon">add</i>
      </button>
    </div>
  );

  const SubmitButton = () => (
    <button
      class="submit mdui-btn mdui-btn-raised mdui-color-theme"
      disabled={publishing}
      onclick={(event) => {
        const $dialog = $(event.target).parents('.mc-editor');
        const editor = $dialog.data('editor-instance');

        onSubmit({
          title: $dialog.find('.editor-title').val(),
          content: editor.getHTML(),
        });
      }}
    >
      {publishing ? '发布中' : '发布'}
    </button>
  );

  return (
    <div
      class={cc([
        'mc-editor',
        {
          'mdui-hidden': !state.editor_open,
          maximize: state.editor_maximize,
          minimize: state.editor_minimize,
          'with-title': withTitle,
          'with-topics': withTopics,
        },
      ])}
      id={id}
      key={`mc-editor-${id}`}
      oncreate={(element) => {
        const $element = $(element);
        const $toolbar = $element.find('.editor-toolbar');
        const $content = $element.find('.editor-content');

        const editor = new Editor($toolbar, $content, {
          menus: [
            'bold',
            'italic',
            'head',
            'code',
            'ol',
            'ul',
            'link',
            'image',
          ],
          placeholder: '请输入内容',
          autoSave: false,
          imageUploadMaxSize: 0,
          imageUploadUrl: `${window.G_API}/images`,
          imageUploadName: 'image',
          imageUploadResponseTransform: (response) => {
            if (response.code === COMMON_IMAGE_UPLOAD_FAILED) {
              response.message = response.extra_message;
            }

            if (response.code === 0) {
              response.data.url = response.data.urls.release;
            }

            return response;
          },
        });

        // 保存编辑器实例到元素中
        $element.data('editor-instance', editor);
      }}
    >
      <Header />
      <div class="body">
        <If condition={withTitle}>
          <Title />
        </If>
        <If condition={withTopics}>
          <Topics />
        </If>
        <div class="editor-content mdui-text-color-theme-text" />
        <div class="editor-toolbar">
          <SubmitButton />
        </div>
      </div>
    </div>
  );
};
