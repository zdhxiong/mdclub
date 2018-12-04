import mdui, { JQ as $ } from 'mdui';
import { Topic } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/lazyComponent';

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
   * 删除该话题
   */
  delete: () => {

  },

  /**
   * 恢复该话题
   */
  restore: () => {

  },

  /**
   * 到编辑界面
   */
  toEdit: () => {

  },

  /**
   * header 元素创建后，绑定滚动事件，使封面随着滚动条滚动
   */
  headerInit: (element) => {
    const $header = $(element);
    const $dialog = $header.parents('.mc-topic-dialog');
    const headerElem = $header[0];
    const dialogElem = $dialog[0];

    $dialog.on('scroll', () => {
      window.requestAnimationFrame(() => {
        headerElem.style['background-position-y'] = `${dialogElem.scrollTop / 2}px`;
      });
    });

    // 向下滚动一段距离
    dialogElem.scrollTo(0, $dialog.width() * 0.56 * 0.58);
  },
});
