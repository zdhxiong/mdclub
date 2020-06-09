import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

/**
 * 数量显示优化
 * 小于 1000 时，直接显示
 * 1000 - 10000 之间时，以 k 为单位，保留一位小数。例如 1.9k
 * 大于 10000 时，以 k 为单位，舍去小数。例如 19k
 *
 * @param badge
 */
const badgeFormat = (badge) => {
  if (badge < 1000) {
    return badge;
  }

  if (badge < 10000) {
    return `${(badge / 1000).toFixed(1)}k`;
  }

  return `${Math.floor(badge / 1000)}k`;
};

export default ({
  cls = '',
  icon,
  iconActive,
  tooltip = '',
  badge = 0,
  active = false,
  onClick,
}) => (
  <button
    class={cc([
      'mc-icon-button',
      'mdui-btn',
      'mdui-btn-icon',
      'mdui-btn-outlined',
      {
        active,
      },
      cls,
    ])}
    mdui-tooltip={`{content: '${tooltip}', delay: 300}`}
    onclick={onClick}
  >
    <If condition={badge}>
      <span class="badge">{badgeFormat(badge)}</span>
    </If>
    <i class="mdui-icon material-icons mdui-text-color-theme-icon">
      {active && iconActive ? iconActive : icon}
    </i>
  </button>
);
