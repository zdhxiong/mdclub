import mdui, { JQ as $ } from 'mdui';
import { User } from 'mdclub-sdk-js';

let Dialog;
let $dialog;
let $content;

const service = {
  following: User.getFollowing,
  followers: User.getFollowers,
  topic_followers: '',
  question_followers: '',
  article_followers: '',
};

export default {
  setState: value => (value),
  getState: () => state => state,

  init: (element) => {
    $dialog = $(element);
    $content = $dialog.find('.mdui-dialog-content');
  },

  /**
   * 打开对话框
   * @param props
   * {type: string, id: int}
   * 用户列表类型 following、followers、topic_followers、question_followers、article_followers
   */
  open: props => (state, actions) => {
    const type = props.type;
    const id = props.id;

    if (!Dialog) {
      Dialog = new mdui.Dialog('.mc-users-dialog', {
        history: false,
      });
    }

    actions.setState({
      type,
      data: [],
      pagination: false,
      loading: true,
    });

    Dialog.open();

    const loaded = (response) => {
      actions.loadEnd();

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        data: actions.getState().data.concat(response.data),
        pagination: response.pagination,
      });
    };

    const infiniteLoad = () => {
      if (actions.getState().loading) {
        return;
      }

      const pagination = actions.getState().pagination;

      if (!pagination) {
        return;
      }

      if (pagination.page >= pagination.total_page) {
        return;
      }

      if ($content[0].scrollHeight - $content[0].scrollTop - $content[0].offsetHeight > 100) {
        return;
      }

      actions.loadStart();

      const data = {
        page: pagination.page + 1,
      };

      service[type](id, data, loaded);
    };

    service[type](id, {}, loaded);

    $content.on('scroll', infiniteLoad);

    $dialog.on('close.mdui.dialog', () => {
      $content.off('scroll', infiniteLoad);
    });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    Dialog.close();
  },

  /**
   * 开始加载
   */
  loadStart: () => ({
    loading: true,
  }),

  /**
   * 结束加载
   */
  loadEnd: () => ({
    loading: false,
  }),
};
