import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { update as updateComment } from 'mdclub-sdk-js/es/CommentApi';
import commonActions from '~/utils/actionsAbstract';
import { loadStart, loadEnd } from '~/utils/loading';
import apiCatch from '~/utils/errorHandler';
import { emit } from '~/utils/pubsub';

const as = {
  /**
   * 打开评论编辑器
   */
  open: (comment) => {
    const onConfirm = (value, dialog) => {
      loadStart();

      updateComment({
        comment_id: comment.comment_id,
        content: value,
        include: ['user'],
      })
        .finally(() => {
          loadEnd();
        })
        .then(({ data }) => {
          dialog.close();
          mdui.snackbar('修改成功');

          emit('datatable_update_row', data);
        })
        .catch(apiCatch);
    };

    const onCancel = () => {};

    const options = {
      confirmText: '保存',
      cancelText: '取消',
      history: false,
      type: 'textarea',
      defaultValue: comment.content,
    };

    mdui.prompt('编辑评论内容', onConfirm, onCancel, options);
  },
};

export default extend(as, commonActions);
