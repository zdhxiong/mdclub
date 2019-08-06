import mdui, { JQ as $ } from 'mdui';
import { Report } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/component';

let dialog; // 对话框实例
let $dialog;
let $dialogContent;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: (element) => {
    $dialog = $(element);
    $dialogContent = $dialog.find('.mdui-dialog-content');
    dialog = new mdui.Dialog($dialog);
  },

  /**
   * 打开对话框
   */
  open: report => (state, actions) => {
    const [reportable_type, reportable_id] = report.key.split(':');

    actions.setState({
      data: [],
      pagination: false,
      loading: true,
    });

    dialog.open();

    const loadSuccess = ({ data, pagination }) => actions.setState({
      loading: false,
      data: actions.getState().data.concat(data),
      pagination,
    });

    const loadFail = ({ message }) => {
      actions.setState({ loading: false });
      mdui.snackbar(message);
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

      if (
          $dialogContent[0].scrollHeight
        - $dialogContent[0].scrollTop
        - $dialogContent[0].offsetHeight > 100
      ) {
        return;
      }

      actions.setState({ loading: true });

      Report
        .getDetailList(reportable_type, reportable_id, { page: pagination.page + 1 })
        .then(loadSuccess)
        .catch(loadFail);
    };

    Report
      .getDetailList(reportable_type, reportable_id, {})
      .then(loadSuccess)
      .catch(loadFail);

    $dialogContent.on('scroll', infiniteLoad);
    $dialog.on('close.mdui.dialog', () => $dialogContent.off('scroll', infiniteLoad));
  },

  /**
   * 关闭对话框
   */
  close: () => {
    dialog.close();
  },
});
