import { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/lazyComponentActions';

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

    // 从 localStorage 中读取主题
    if (!state.theme) {
      let theme = window.localStorage.getItem('admin_theme');
      if (!theme) {
        theme = 'light';
      }

      actions.setState({ theme });
      actions.setTheme(theme);
    }
  },

  /**
   * 主题切换
   */
  toggleTheme: () => (state, actions) => {
    const theme = state.theme === 'light' ? 'dark' : 'light';

    window.localStorage.setItem('admin_theme', theme);
    actions.setState({ theme });
    actions.setTheme(theme);
  },

  /**
   * 设置主题
   */
  setTheme: theme => (state, actions) => {
    $body
      .removeClass('mdui-theme-layout-light mdui-theme-layout-dark')
      .addClass(`mdui-theme-layout-${theme}`);
  },
});
