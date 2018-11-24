import { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/actions';

const $body = $('body');

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    $(props.element).mutation();

    // 从 HTML 文件中读取当前登录用户
    const user = window.G_USER;
    if (user) {
      actions.setState({ user });
      window.G_USER = null;
    }
  },

  /**
   * 主题切换
   */
  toggleTheme: () => (state, actions) => {
    const theme = state.theme === 'light' ? 'dark' : 'light';

    actions.setState({ theme });

    $body
      .removeClass('mdui-theme-layout-light mdui-theme-layout-dark')
      .addClass(`mdui-theme-layout-${theme}`);
  },
});
