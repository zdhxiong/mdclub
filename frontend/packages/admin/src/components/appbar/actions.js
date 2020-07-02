import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import commonActions from '~/utils/actionsAbstract';
import { removeCookie } from '~/utils/cookie';

const as = {
  onCreate: ({ element }) => (_, actions) => {
    $(element).mutation();

    // 从 HTML 文件中读取当前登录用户
    actions.setState({ user: window.G_USER });
    window.G_USER = null;
  },

  /**
   * 退出登录
   */
  logout: () => {
    removeCookie('token');
    window.localStorage.removeItem('token');
    window.location.href = `${window.G_ROOT}/`;
  },
};

export default extend(as, commonActions);
