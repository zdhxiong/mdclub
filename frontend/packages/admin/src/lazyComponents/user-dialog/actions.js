import mdui, { JQ as $ } from 'mdui';
import { User } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/lazyComponentActions';

let Dialog; // 对话框实例

export default $.extend({}, actionsAbstract, {

  /**
   * 打开对话框
   */
  open: user_id => (state, actions) => {
    if (!Dialog) {
      Dialog = new mdui.Dialog('.mc-user-dialog');
    }

    actions.setState({ loading: true });

    Dialog.open();

    User.getOne(user_id, (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        Dialog.close();
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({ user: response.data });
    });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    Dialog.close();
  },

  /**
   * cover 元素创建后，绑定滚动事件，使封面随着滚动条滚动
   */
  coverInit: element => {
    const $cover = $(element);
    const $dialog = $cover.parents('.mc-user-dialog');

    $dialog.on('scroll', () => {
      window.requestAnimationFrame(() => {
        $cover[0].style['background-position-y'] = `${$dialog[0].scrollTop / 2}px`;
      });
    });

    // 向下滚动一段距离
    $dialog[0].scrollTo(0, $dialog.width() * 0.56 * 0.58);
  },
});
