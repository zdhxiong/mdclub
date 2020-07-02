import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import {
  get as getComment,
  del as deleteComment,
} from 'mdclub-sdk-js/es/CommentApi';
import commonActions from '~/utils/actionsAbstract';
import { loadStart } from '~/utils/loading';
import { emit } from '~/utils/pubsub';
import { apiCatch } from '~/utils/errorHandlers';

let dialog;

const as = {
  onCreate: ({ element }) => {
    dialog = new mdui.Dialog(element, {
      history: false,
    });
  },

  /**
   * 打开对话框
   * @param comment
   * 该参数为整型时，表示为评论ID，需要根据该ID获取评论信息；
   * 若该参数为对象，表示为完整的评论信息，不需要再请求数据
   */
  open: (comment) => (state, actions) => {
    const isComplete = !isNumber(comment);

    actions.setState({
      comment: isComplete ? comment : null,
      loading: !isComplete,
    });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    getComment({
      comment_id: comment,
      include: ['user'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ comment: data });

        setTimeout(() => dialog.handleUpdate());
      })
      .catch((response) => {
        dialog.close();
        apiCatch(response);
      });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    dialog.close();
  },

  /**
   * 删除该评论
   */
  delete: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认要删除？')) {
      return;
    }

    loadStart();
    actions.close();

    deleteComment({ comment_id: state.comment.comment_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    emit('comment_edit_open', state.comment);

    actions.close();
  },
};

export default extend(as, commonActions);
