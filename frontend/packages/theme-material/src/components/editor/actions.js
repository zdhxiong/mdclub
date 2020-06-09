import $ from 'mdui.jq';
import currentUser from '~/utils/currentUser';
import { emit } from '~/utils/pubsub';

/**
 * 在需要用到编辑器的页面中，引入该 actions
 */
export default {
  /**
   * 打开编辑器
   */
  editorOpen: () => (state, actions) => {
    if (!currentUser()) {
      emit('login_open');
      return;
    }

    // 读取 localStorage 中保存的话题
    try {
      const selected = JSON.parse(
        window.localStorage.getItem(`${state.auto_save_key}-topics`),
      );
      if (selected) {
        actions.setState({
          editor_selected_topics: selected,
          editor_selected_topic_ids: selected.map((topic) => topic.topic_id),
        });
      }
      // eslint-disable-next-line no-empty
    } catch (err) {}

    actions.setState({ editor_open: true });
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
