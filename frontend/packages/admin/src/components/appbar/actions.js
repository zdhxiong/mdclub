import { JQ as $ } from 'mdui';
import Cookies from 'js-cookie';
import { themeReverse, setTheme } from './helper';
import actionsAbstract from '../../abstracts/actions/component';

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    $(props.element).mutation();

    // 从 HTML 文件中读取当前登录用户
    if (window.G_USER) {
      actions.setState({ user: window.G_USER });
      window.G_USER = null;
    }

    // 从 localStorage 中读取主题
    if (!state.theme) {
      const theme = window.localStorage.getItem('admin_theme') || 'light';
      setTheme(theme);
      actions.setState({ theme });
    }
  },

  /**
   * 主题切换
   */
  toggleTheme: () => (state, actions) => {
    const theme = themeReverse(state.theme);

    window.localStorage.setItem('admin_theme', theme);
    setTheme(theme);
    actions.setState({ theme });
  },

  /**
   * 退出登录
   */
  logout: () => {
    Cookies.remove('token');
    window.location.href = window.G_ROOT;
  },
});
