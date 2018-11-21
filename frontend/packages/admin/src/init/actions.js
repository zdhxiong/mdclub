import { location } from '@hyperapp/router';
import mdui, { JQ as $ } from 'mdui';

export default {
  location: location.actions,

  // 设置和获取状态
  setState: value => (value),
  getState: () => _state => _state,

  // 路由切换后的回调
  routeChange: () => {
    // 回到页面顶部
    window.scrollTo(0, 0);

    // 在手机和平板时，切换路由后关闭抽屉导航
    if (window.innerWidth < 1024) {
      (new mdui.Drawer('.mc-drawer')).close();
    }
  },

  components: {

  },
};
