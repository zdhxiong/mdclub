import { $body } from 'mdui/es/utils/dom';

export default {
  /**
   * 设置应用布局色
   */
  setLayout: (layout) => {
    $body[layout === 'dark' ? 'addClass' : 'removeClass'](
      'mdui-theme-layout-dark',
    );
  },
};
