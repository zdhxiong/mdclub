import { h } from 'hyperapp';
import cc from 'classcat';
import map from 'mdui.jq/es/functions/map';
import './index.less';

/**
 * 用于回答列表和评论列表头部
 * @params show 是否显示
 * @params title 标题
 * @params disabled 是否禁用排序按钮
 * @params currentOrder 当前排序值
 * @params orders 当前排序值和名称组成的数组
 * @params key 附加在 id 上
 * @params closeBtnClick 若在手机上，在 dialog 中显示时，需要关闭按钮，需要传入回调函数
 */
export default ({
  show,
  title,
  disabled,
  currentOrder,
  orders,
  onChangeOrder,
  key = '',
  closeBtnClick = false,
}) => (
  <div
    class={cc([
      'mc-list-header',
      {
        'mdui-hidden': !show,
      },
    ])}
  >
    <div class="title">
      <If condition={closeBtnClick}>
        <button
          class="close mdui-btn mdui-btn-icon mdui-ripple"
          onclick={closeBtnClick}
        >
          <i class="mdui-icon material-icons">close</i>
        </button>
      </If>
      {title}
    </div>
    <button
      class="mdui-btn mdui-btn-dense"
      mdui-menu={`{target: '#mc-list-header-${key}', align: 'right'}`}
      disabled={disabled}
    >
      {
        map(orders, ({ order, name }) => {
          return order === currentOrder ? name : null;
        })[0]
      }
      {orders[currentOrder]}
      <i class="mdui-icon mdui-icon-right material-icons mdui-text-color-theme-icon">
        arrow_drop_down
      </i>
    </button>
    <ul class="mdui-menu" id={`mc-list-header-${key}`}>
      {orders.map(({ order, name }) => (
        <li class="mdui-menu-item" onclick={() => onChangeOrder(order)}>
          {/* eslint-disable-next-line no-script-url */}
          <a href="javascript:void(0)">
            <i class="mdui-menu-item-icon mdui-icon material-icons">
              <If condition={currentOrder === order}>check</If>
            </i>
            {name}
          </a>
        </li>
      ))}
    </ul>
  </div>
);
