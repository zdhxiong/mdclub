import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import {
  get as getAnswer,
  del as deleteAnswer,
} from 'mdclub-sdk-js/es/AnswerApi';
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
   * @param answer
   * 该参数为整型时，表示为回答ID，需要根据该ID获取回答信息；
   * 该参数为对象时，表示为完整的回答信息，不需要再请求数据
   */
  open: (answer) => (state, actions) => {
    const isComplete = !isNumber(answer);

    actions.setState({
      answer: isComplete ? answer : null,
      loading: !isComplete,
    });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    getAnswer({
      answer_id: answer,
      include: ['user', 'question'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ answer: data });

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
   * 删除该回答
   */
  delete: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认要删除？')) {
      return;
    }

    loadStart();
    actions.close();

    deleteAnswer({ answer_id: state.answer.answer_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    emit('answer_edit_open', state.answer);

    actions.close();
  },
};

export default extend(as, commonActions);
