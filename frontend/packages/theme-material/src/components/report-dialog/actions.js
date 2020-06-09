import $ from 'mdui.jq';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import { create as createReport } from 'mdclub-sdk-js/es/ReportApi';
import commonActions from '~/utils/actionsAbstract';
import apiCatch from '~/utils/errorHandler';
import stateDefault from './stateDefault';

let dialog;

const as = {
  onCreate: ({ element }) => (_, actions) => {
    const $element = $(element).mutation();

    dialog = new mdui.Dialog($element, {
      history: false,
    });

    dialog.$element.on('confirm.mdui.dialog', () => {
      const $customReason = dialog.$element.find('.custom-reason');
      const customReason = $customReason.val();
      const { type, item, reason } = actions.getState();
      const getReportableId = () => {
        switch (type) {
          case 'question':
            return item.question_id;
          case 'answer':
            return item.answer_id;
          case 'article':
            return item.article_id;
          case 'comment':
            return item.comment_id;
          case 'user':
            return item.user_id;
          default:
            return null;
        }
      };

      createReport({
        reportable_type: type,
        reportable_id: getReportableId(),
        reason: reason === '其他原因' ? customReason : reason,
      })
        .finally(() => {
          actions.setState(stateDefault);
          $customReason.val('');
        })
        .then(() => {
          mdui.snackbar('举报成功');
        })
        .catch((response) => {
          if (response.code === COMMON_FIELD_VERIFY_FAILED) {
            mdui.snackbar(Object.values(response.errors)[0]);
            return;
          }

          apiCatch(response);
        });

      dialog.close();
    });
  },

  /**
   * 选择原因
   */
  onChange: (event) => (state, actions) => {
    const reason = event.target.value;

    actions.setState({ reason });

    if ([reason, state.reason].indexOf('其他原因') > -1) {
      setTimeout(() => dialog.handleUpdate());
    }
  },

  /**
   * 打开举报弹框
   */
  open: ({ type, item }) => (_, actions) => {
    actions.setState({ type, item });
    dialog.open();
  },

  /**
   * 关闭举报弹框
   */
  close: () => {
    dialog.close();
  },
};

export default extend(as, commonActions);
