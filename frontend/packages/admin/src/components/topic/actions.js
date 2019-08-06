import mdui, { JQ as $ } from 'mdui';
import { Topic } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
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

    actions.setState({
      topic: isComplete ? topic : false,
      loading: !isComplete,
    });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    Topic
      .getOne(topic)
      .then(({ data }) => {
        actions.setState({ loading: false, topic: data });

        setTimeout(() => dialog.handleUpdate());
      })
      .catch(({ message }) => {
        actions.setState({ loading: false });
        dialog.close();
        mdui.snackbar(message);
      });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    dialog.close();
  },

  /**
   * 删除该话题
   */
  delete: () => (state, actions) => {
    /* eslint-disable */
    if (!confirm('确认要删除？你仍可以在回收站中恢复该话题。')) {
      return;
    }
    /* eslint-enable */

    loading.start();
    actions.close();

    Topic
      .deleteOne(state.topic.topic_id)
      .then(global_actions.topics.deleteSuccess)
      .catch(global_actions.topics.deleteFail);
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
