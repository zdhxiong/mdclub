import mdui, { JQ as $ } from 'mdui';

export default {
  setState: value => (value),
  getState: () => state => state,

  /**
   * 路由切换后的回调
   */
  routeChange: () => {
    window.scrollTo(0, 0);

    // 在手机和平板时，切换路由后关闭抽屉导航
    if (window.innerWidth < 1024) {
      (new mdui.Drawer('.me-drawer')).close();
    }
  },

  /**
   * 删除后的回调
   */
  deleteSuccess: ({ code, message }) => (state, actions) => {
    $.loadEnd();

    if (code) {
      mdui.snackbar(message);
    } else {
      actions.loadData();
    }
  },
};
