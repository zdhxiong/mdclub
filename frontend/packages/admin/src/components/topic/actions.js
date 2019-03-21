import mdui, { JQ as $ } from 'mdui';
import { Topic } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/component';

let global_actions;
let dialog;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: (props) => {
    global_actions = props.global_actions;

    dialog = new mdui.Dialog('.mc-topic');
  },

  /**
   * 打开对话框
   */
  open: topic => (state, actions) => {
    actions.setState({ topic });

    setTimeout(() => dialog.open());
  },

  /**
   * 关闭对话框
   */
  close: () => dialog.close(),

  /**
   * 删除该话题
   */
  delete: () => (state, actions) => {
    if (!confirm('确认要删除？你仍可以在回收站中恢复该话题。')) {
      return;
    }

    $.loadStart();
    actions.close();
    Topic.deleteOne(state.topic.topic_id, global_actions.topics.deleteSuccess);
  },

  /**
   * 恢复该话题
   */
  restore: () => {

  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    global_actions.components.topicEdit.open(state.topic);

    actions.close();
  },
});
