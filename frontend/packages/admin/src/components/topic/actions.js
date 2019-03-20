import mdui, { JQ as $ } from 'mdui';
import { Topic } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/component';

let dialog;

export default $.extend({}, actionsAbstract, {
  /**
   * 打开对话框
   */
  open: topic => (state, actions) => {
    actions.setState({ topic });

    if (!dialog) {
      dialog = new mdui.Dialog('.mc-topic');
    }

    // 待数据渲染完成后再打开
    setTimeout(() => dialog.open());
  },

  /**
   * 关闭对话框
   */
  close: () => dialog.close(),

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
});
