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
   * @param topic 该参数为整型时，表示为话题ID，需要根据该ID获取话题信息；若该参数为对象，表示为完整的话题信息，不需要再请求数据
   */
  open: topic => (state, actions) => {
    const isComplete = typeof topic === 'object';

    isComplete
      ? actions.setState({ topic, loading: false})
      : actions.setState({ topic: false, loading: true });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    Topic.getOne(topic, (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        dialog.close();
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({ topic: response.data });

      setTimeout(() => dialog.handleUpdate());
    });
  },

  /**
   * 关闭对话框
   */
  close: () => dialog.close(),

  /**
   * 删除该话题
   */
  delete: () => (state, actions) => {
    /* eslint-disable */
    if (!confirm('确认要删除？你仍可以在回收站中恢复该话题。')) {
      return;
    }
    /* eslint-enable */

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
