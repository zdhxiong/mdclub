import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import followActions from '~/components/follow/actions';

const $title = $('title');

/**
 * 所有 actions 通用
 */
const as = {
  /**
   * 设置状态
   */
  setState: (value) => value,

  /**
   * 获取状态
   */
  getState: () => (state) => state,

  /**
   * 设置网页 title
   */
  setTitle: (title) => {
    const metaTitle = title
      ? `${title} - ${window.G_OPTIONS.site_name}`
      : window.G_OPTIONS.site_name +
        (window.G_OPTIONS.site_description
          ? ` - ${window.G_OPTIONS.site_description}`
          : '');

    $title.text(metaTitle);
  },
};

export default extend(as, followActions);
