import mdui, { JQ as $ } from 'mdui';
import { Topic } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/lazyComponentActions';

let Dialog;

export default $.extend({}, actionsAbstract, {
  /**
   * 打开对话框
   */
  open: topic_id => (state, actions) => {
    if (!Dialog) {
      Dialog = new mdui.Dialog('.mc-topic-dialog');

      Dialog.$dialog.on('closed.mdui.dialog', () => {
        actions.setState({
          topic: false,
          loading: false,
        });
      });
    }

    actions.setState({ loading: true });

    Dialog.open();

    Topic.getOne(topic_id, (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        Dialog.close();
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({ topic: response.data });
    });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    Dialog.close();
  },

  /**
   * header 元素创建后，绑定滚动事件，使封面随着滚动条滚动
   */
  headerInit: (element) => {

  },
});
