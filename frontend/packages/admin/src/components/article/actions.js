import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import {
  get as getArticle,
  del as deleteArticle,
} from 'mdclub-sdk-js/es/ArticleApi';
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
   * @param article
   * 该参数为整型时，表示为文章ID，需要根据该ID获取文章信息
   * 该参数为对象时，表示为完整的文章信息，不需要再请求数据
   */
  open: (article) => (state, actions) => {
    const isComplete = !isNumber(article);

    actions.setState({
      article: isComplete ? article : null,
      loading: !isComplete,
    });

    setTimeout(() => dialog.open());

    if (isComplete) {
      return;
    }

    getArticle({
      article_id: article,
      include: ['user', 'topics'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ article: data });

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

    deleteArticle({ article_id: state.article.article_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    emit('article_edit_open', state.article);

    actions.close();
  },
};

export default extend(as, commonActions);
