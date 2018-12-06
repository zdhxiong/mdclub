import mdui, { JQ as $ } from 'mdui';
import { User } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/lazyComponent';

let Dialog; // 对话框实例

export default $.extend({}, actionsAbstract, {
  /**
   * 打开对话框
   */
  open: user_id => (state, actions) => {
    if (!Dialog) {
      Dialog = new mdui.Dialog('.mc-dialog-user');

      Dialog.$dialog.on('closed.mdui.dialog', () => {
        actions.setState({
          user: false,
          loading: false,
        });
      });
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
   * 禁用该账号
   */
  disable: () => {

  },

  /**
   * 启用该账号
   */
  enable: () => {

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
    const $dialog = $header.parents('.mc-dialog-user');
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
