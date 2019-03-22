import mdui, { JQ as $ } from 'mdui';
import { User } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/component';

let global_actions;
let dialog;
let $dialog;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: (props) => {
    global_actions = props.global_actions;

    dialog = new mdui.Dialog('.mc-user');
    $dialog = dialog.$dialog;
  },

  /**
   * 打开对话框
   * @param user 该参数为整型时，表示为用户ID，需要根据该ID获取用户信息；若该参数为对象，表示为完整的用户信息，不需要再请求数据
   */
  open: user => (state, actions) => {
    const isComplete = typeof user === 'object';

    isComplete
      ? actions.setState({ user, loading: false })
      : actions.setState({ user: false, loading: true });

    dialog.open();

    setTimeout(() => actions.headerReset());

    if (isComplete) {
      return;
    }

    User.getOne(user, (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        dialog.close();
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({ user: response.data });

      setTimeout(() => dialog.handleUpdate());
    });
  },

  /**
   * 关闭对话框
   */
  close: () => dialog.close(),

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
   * 重置 header 部分滚动条位置
   */
  headerReset: () => {
    // 向下滚动一段距离
    $dialog[0].scrollTo(0, $dialog.width() * 0.56 * 0.58);
  },

  /**
   * header 元素创建后，绑定滚动事件，使封面随着滚动条滚动
   */
  headerInit: () => (state, actions) => {
    const headerElem = $dialog.find('.header')[0];
    const dialogElem = $dialog[0];

    $dialog.on('scroll', () => {
      window.requestAnimationFrame(() => {
        headerElem.style['background-position-y'] = `${dialogElem.scrollTop / 2}px`;
      });
    });

    actions.headerReset();
  },
});
