import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import {
  get as getUser,
  disable as disableUser,
  enable as enableUser,
} from 'mdclub-sdk-js/es/UserApi';
import commonActions from '~/utils/actionsAbstract';
import { loadStart } from '~/utils/loading';
import { emit } from '~/utils/pubsub';
import { apiCatch } from '~/utils/errorHandlers';

let dialog;

const as = {
  onCreate: () => {
    dialog = new mdui.Dialog('.mc-user', {
      history: false,
    });
  },

  /**
   * 打开对话框
   * @param user
   * 该参数为整型时，表示为用户ID，需要根据该ID获取用户信息；
   * 若该参数为对象，表示为完整的用户信息，不需要再请求数据
   */
  open: (user) => (state, actions) => {
    const isComplete = !isNumber(user);

    actions.setState({
      user: isComplete ? user : null,
      loading: !isComplete,
    });

    dialog.open();

    setTimeout(() => actions.headerReset());

    if (isComplete) {
      return;
    }

    getUser({ user_id: user })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ user: data });

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
   * 禁用该账号
   */
  disable: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认要禁用？')) {
      return;
    }

    loadStart();
    actions.close();

    disableUser({ user_id: state.user.user_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 启用该账号
   */
  enable: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认要启用？')) {
      return;
    }

    loadStart();
    actions.close();

    enableUser({ user_id: state.user.user_id })
      .then(actions.deleteSuccess)
      .catch(actions.deleteFail);
  },

  /**
   * 到编辑界面
   */
  toEdit: () => (state, actions) => {
    emit('user_edit_open', state.user);

    actions.close();
  },

  /**
   * 重置 header 部分滚动条位置（向下滚动一段距离）
   */
  headerReset: () => {
    dialog.$element[0].scrollTo(0, dialog.$element.width() * 0.56 * 0.58);
  },

  /**
   * header 元素创建后，绑定滚动事件，使封面随着滚动条滚动
   */
  onHeaderCreate: () => (_, actions) => {
    const headerElem = dialog.$element.find('.header')[0];

    dialog.$element.on('scroll', () => {
      window.requestAnimationFrame(() => {
        headerElem.style['background-position-y'] = `${
          dialog.$element[0].scrollTop / 2
        }px`;
      });
    });

    actions.headerReset();
  },
};

export default extend(as, commonActions);
