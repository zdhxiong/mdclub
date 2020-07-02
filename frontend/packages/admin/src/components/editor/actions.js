import $ from 'mdui.jq';
import { isUndefined } from 'mdui.jq/es/utils';

/**
 * 在需要用到编辑器的页面中，引入该 actions
 */
export default {
  /**
   * 打开编辑器
   */
  editorOpen: (item) => (state, actions) => {
    // 设置话题
    if (
      !isUndefined(item.relationships.topics) &&
      item.relationships.topics.length
    ) {
      actions.setState({
        editor_selected_topics: item.relationships.topics,
        editor_selected_topic_ids: item.relationships.topics.map(
          (topic) => topic.topic_id,
        ),
      });
    }

    // 设置标题
    if (!isUndefined(item.title)) {
      $('.editor-title').val(item.title);
    }

    // 获取主键名
    const getPrimaryKey = () => {
      if (!isUndefined(item.answer_id)) {
        return 'answer_id';
      }

      if (!isUndefined(item.question_id)) {
        return 'question_id';
      }

      return 'article_id';
    };

    // 获取编辑器实例
    const getEditorElement = () => {
      if (!isUndefined(item.answer_id)) {
        return $('.mc-answer-edit .mc-editor');
      }

      if (!isUndefined(item.question_id)) {
        return $('.mc-question-edit .mc-editor');
      }

      return $('.mc-article-edit .mc-editor');
    };

    // 设置主键ID
    const $editor = getEditorElement();
    $editor.data('primary_id', item[getPrimaryKey()]);

    // 设置内容
    const editor = $editor.data('editor-instance');
    editor.setHTML(item.content_rendered);

    actions.setState({
      editor_open: true,
      editor_minimize: false,
      editor_maximize: true,
    });
    actions.editorUpdateOverlay();
  },

  /**
   * 关闭编辑器
   */
  editorClose: () => (_, actions) => {
    actions.setState({ editor_open: false });
    actions.editorUpdateOverlay();
  },

  /**
   * 最小化编辑器
   */
  editorMinimize: () => (state, actions) => {
    actions.setState({
      editor_minimize: !state.editor_minimize,
    });

    actions.editorUpdateOverlay();
  },

  /**
   * 最大化编辑器
   */
  editorMaximize: () => (state, actions) => {
    actions.setState({
      editor_minimize: false,
      editor_maximize: !state.editor_maximize,
    });

    actions.editorUpdateOverlay();
  },

  /**
   * 全屏显示编辑器时，需要显示遮罩，否则隐藏遮罩
   */
  editorUpdateOverlay: () => (_, actions) => {
    const state = actions.getState();

    if (state.editor_open && state.editor_maximize && !state.editor_minimize) {
      $.showOverlay();
    } else {
      $.hideOverlay();
    }
  },
};
