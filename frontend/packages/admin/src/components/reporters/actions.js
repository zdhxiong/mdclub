import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { getReasons } from 'mdclub-sdk-js/es/ReportApi';
import commonActions from '~/utils/actionsAbstract';
import { apiCatch } from '~/utils/errorHandlers';

let dialog; // 对话框实例
let $dialogContent;
let reportable_type;
let reportable_id;

const as = {
  /**
   * 初始化
   */
  onCreate: ({ element }) => (state, actions) => {
    dialog = new mdui.Dialog(element, {
      history: false,
    });

    $dialogContent = dialog.$element
      .find('.mdui-dialog-content')
      .on('scroll', actions.infiniteLoad);

    dialog.$element.on('close.mdui.dialog', () => {
      $dialogContent.off('scroll', actions.infiniteLoad);
    });
  },

  /**
   * 下拉加载更多
   */
  infiniteLoad: () => (state, actions) => {
    if (state.loading) {
      return;
    }

    const { pagination } = state;

    if (!pagination || pagination.page >= pagination.pages) {
      return;
    }

    if (
      $dialogContent[0].scrollHeight -
        $dialogContent[0].scrollTop -
        $dialogContent[0].offsetHeight >
      100
    ) {
      return;
    }

    actions.setState({ loading: true });

    getReasons({
      reportable_type,
      reportable_id,
      page: pagination.page + 1,
      include: ['reporter'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then((response) => {
        actions.setState({
          data: state.data.concat(response.data),
          pagination: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 打开对话框
   */
  open: (report) => (state, actions) => {
    [reportable_type, reportable_id] = report.key.split(':');

    actions.setState({
      data: [],
      pagination: null,
      loading: true,
    });

    dialog.open();

    getReasons({
      reportable_type,
      reportable_id,
      include: ['reporter'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data, pagination }) => {
        actions.setState({
          data,
          pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 关闭对话框
   */
  close: () => {
    dialog.close();
  },
};

export default extend(as, commonActions);
