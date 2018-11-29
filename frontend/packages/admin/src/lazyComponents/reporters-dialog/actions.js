import mdui, { JQ as $ } from 'mdui';
import { Report } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/lazyComponentActions';

let Dialog; // 对话框实例
let $dialog;
let $content;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: (element) => {
    $dialog = $(element);
    $content = $dialog.find('.mdui-dialog-content');
  },

  /**
   * 打开对话框
   */
  open: ({ reportable_type, reportable_id }) => (state, actions) => {
    if (!Dialog) {
      Dialog = new mdui.Dialog('.mc-reporters-dialog');
    }

    actions.setState({
      data: [],
      pagination: false,
      loading: true,
    });

    Dialog.open();

    const loaded = (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        data: actions.getState().data.concat(response.data),
        pagination: response.pagination,
      });
    };

    const infiniteLoad = () => {
      if (actions.getState().loading) {
        return;
      }

      const pagination = actions.getState().pagination;

      if (!pagination) {
        return;
      }

      if (pagination.page >= pagination.pages) {
        return;
      }

      if ($content[0].scrollHeight - $content[0].scrollTop - $content[0].offsetHeight > 100) {
        return;
      }

      actions.setState({ loading: true });

      const data = {
        page: pagination.page + 1,
      };

      Report.getDetailList(reportable_type, reportable_id, data, loaded);
    };

    Report.getDetailList(reportable_type, reportable_id, {}, loaded);

    $content.on('scroll', infiniteLoad);
    $dialog.on('close.mdui.dialog', () => {
      $content.off('scroll', infiniteLoad);
    });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    Dialog.close();
  }
});
