import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import { get as getTopic, del as deleteTopic } from 'mdclub-sdk-js/es/TopicApi';
import commonActions from '~/utils/actionsAbstract';
import { loadStart } from '~/utils/loading';
import { emit } from '~/utils/pubsub';
import { apiCatch } from '~/utils/errorHandlers';

let dialog;

const as = {
  onCreate: () => {
    dialog = new mdui.Dialog('.mc-topic', {
      history: false,
    });
  },

  /**
   * 打开对话框
   * @param topic
   * 该参数为整型时，表示为话题ID，需要根据该ID获取话题信息；
   * 若该参数为对象，表示为完整的话题信息，不需要再请求数据
   */
  open: (topic) => (state, actions) => {
    const isComplete = !isNumber(topic);

    actions.setState({
      topic: isComplete ? topic : null,
      loading: !isComplete,
    });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    getTopic({ topic_id: topic })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ topic: data });

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
   * 删除该话题
   */
  delete: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认要删除？')) {
      return;
    }

    loadStart();
    actions.close();

    deleteTopic({ topic_id: state.topic.topic_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    emit('topic_edit_open', state.topic);

    actions.close();
  },
};

export default extend(as, commonActions);
