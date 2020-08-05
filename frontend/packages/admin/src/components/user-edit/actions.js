import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import { isNumber } from 'mdui.jq/es/utils';
import {
  get as getUser,
  update as updateUser,
  deleteAvatar,
  deleteCover,
} from 'mdclub-sdk-js/es/UserApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadEnd, loadStart } from '~/utils/loading';
import apiCatch from '~/utils/errorHandler';

let dialog;

const as = {
  onCreate: ({ element }) => (state, actions) => {
    dialog = new mdui.Dialog(element, {
      history: false,
    });

    dialog.$element.on('open.mdui.dialog', () => {
      setTimeout(() => {
        dialog.$element.find('input[name="headline"]')[0].focus();
        mdui.updateTextFields(dialog.$element.find('.mdui-textfield'));
        dialog.handleUpdate();
        actions.headerReset();
      });
    });
  },

  /**
   * 打开对话框
   * @param user
   * 若user为null，表示添加数据；（目前没有添加数据功能）
   * 若user为整型，则需要先根据该参数获取用户信息；
   * 若user为对象，则不需要再获取用户信息
   */
  open: (user = null) => (state, actions) => {
    const isComplete = !isNumber(user);

    if (isComplete) {
      actions.setState({
        user_id: user.user_id,
        username: user.username,
        avatar: user.avatar,
        cover: user.cover,
        headline: user.headline,
        blog: user.blog,
        company: user.company,
        location: user.location,
        bio: user.bio,
        loading: false,
      });
    } else {
      actions.setState({
        user_id: user,
        username: '',
        avatar: '',
        cover: '',
        headline: '',
        blog: '',
        company: '',
        location: '',
        bio: '',
        loading: true,
      });
    }

    dialog.open();

    if (isComplete) {
      return;
    }

    // 只传入了 ID，获取用户详情
    getUser({ user_id: user })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({
          user_id: data.user_id,
          username: data.username,
          avatar: data.avatar,
          cover: data.cover,
          headline: data.headline,
          blog: data.blog,
          company: data.company,
          location: data.location,
          bio: data.bio,
        });

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
   * 提交保存
   */
  submit: () => (state, actions) => {
    loadStart();

    // 修改用户信息
    if (state.user_id) {
      const $form = $('.mc-user-edit form');
      const formData = {};

      $form.serializeArray().forEach((item) => {
        formData[item.name] = item.value;
      });

      formData.user_id = state.user_id;

      updateUser(formData)
        .finally(() => {
          loadEnd();
        })
        .then(({ data }) => {
          actions.close();
          mdui.snackbar('修改成功');

          emit('datatable_update_row', data);
        })
        .catch(apiCatch);
    }
  },

  /**
   * 删除头像
   */
  deleteAvatar: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认删除头像？')) {
      return;
    }

    deleteAvatar({ user_id: state.user_id })
      .then(({ data }) => {
        actions.setState({ avatar: data });
      })
      .catch(apiCatch);
  },

  /**
   * 删除封面
   */
  deleteCover: () => (state, actions) => {
    // eslint-disable-next-line no-restricted-globals,no-alert
    if (!confirm('确认删除封面？')) {
      return;
    }

    deleteCover({ user_id: state.user_id })
      .then(({ data }) => {
        actions.setState({ cover: data });
      })
      .catch(apiCatch);
  },

  /**
   * 重置 header 部分滚动条位置（向下滚动一段距离）
   */
  headerReset: () => {
    dialog.$element
      .find('.mdui-dialog-content')[0]
      .scrollTo(0, dialog.$element.width() * 0.56 * 0.58);
  },

  /**
   * header 元素创建后，绑定滚动事件，使封面随着滚动条滚动
   */
  onHeaderCreate: () => (_, actions) => {
    setTimeout(() => {
      const $content = dialog.$element.find('.mdui-dialog-content');
      const headerElem = dialog.$element.find('.header')[0];

      $content.on('scroll', () => {
        window.requestAnimationFrame(() => {
          headerElem.style['background-position-y'] = `${
            $content[0].scrollTop / 2
          }px`;
        });
      });

      actions.headerReset();
    });
  },
};

export default extend(as, commonActions);
