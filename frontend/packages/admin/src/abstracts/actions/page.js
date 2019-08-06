import mdui, { JQ as $ } from 'mdui';
import loading from '../../helper/loading';

export default {
  setState: value => (value),
  getState: () => state => state,

  /**
   * 路由切换后的回调
   */
  routeChange: (title = '') => {
    // 滚动回页面顶部
    window.scrollTo(0, 0);

    // 在手机和平板时，切换路由后关闭抽屉导航
    if (window.innerWidth < 1024) {
      const drawer = new mdui.Drawer('.me-drawer');
      drawer.close();
    }

    // 设置页面标题
    $('title').text(title);
  },

  /**
   * 删除成功后，重新载入数据
   */
  deleteSuccess: () => (state, actions) => {
    loading.end();
    actions.loadData();
  },

  /**
   * 删除失败
   */
  deleteFail: ({ message }) => {
    loading.end();
    mdui.snackbar(message);
  },
};
