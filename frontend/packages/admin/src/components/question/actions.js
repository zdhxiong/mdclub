import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import {
  get as getQuestion,
  del as deleteQuestion,
} from 'mdclub-sdk-js/es/QuestionApi';
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
   * @param question
   * 该参数为整型时，表示为提问ID，需要根据该ID获取提问信息；
   * 该参数为对象时，表示为完整的提问信息，不需要再请求数据
   */
  open: (question) => (state, actions) => {
    const isComplete = !isNumber(question);

    actions.setState({
      question: isComplete ? question : null,
      loading: !isComplete,
    });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    getQuestion({
      question_id: question,
      include: ['user', 'topics'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ question: data });

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
   * 删除该提问
   */
  delete: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认要删除？')) {
      return;
    }

    loadStart();
    actions.close();

    deleteQuestion({ question_id: state.question.question_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    emit('question_edit_open', state.question);

    actions.close();
  },
};

export default extend(as, commonActions);
