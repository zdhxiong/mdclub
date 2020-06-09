import $ from 'mdui.jq';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import commonActions from '~/utils/actionsAbstract';

let dialog;

const as = {
  onCreate: ({ element }) => {
    const $element = $(element).mutation();

    dialog = new mdui.Dialog($element, {
      history: false,
    });
  },

  /**
   * 打开分享弹框
   */
  open: ({ url, title }) => (_, actions) => {
    actions.setState({ url, title });

    dialog.open();
  },

  /**
   * 关闭分享弹框
   */
  close: () => {
    dialog.close();
  },

  /**
   * 分享到微博
   */
  shareToWeibo: () => (state, actions) => {
    actions.close();

    window.open(
      `https://service.weibo.com/share/share.php?url=${state.url}&title=${state.title}`,
    );
  },

  /**
   * 分享到 Facebook
   */
  shareToFacebook: () => (state, actions) => {
    actions.close();

    window.open(`https://www.facebook.com/sharer/sharer.php?u=${state.url}`);
  },

  /**
   * 分享到 Twitter
   */
  shareToTwitter: () => (state, actions) => {
    actions.close();

    window.open(`https://twitter.com/intent/tweet?url=${state.url}`);
  },
};

export default extend(as, commonActions);
