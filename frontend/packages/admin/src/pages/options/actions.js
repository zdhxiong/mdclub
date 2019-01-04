import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Option } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange();
    global_actions = props.global_actions;
    global_actions.lazyComponents.searchBar.setState({ isNeedRender: false });

    mdui.mutation();

    const $element = $(props.element);
    const $appbar = $('.mc-appbar');
    const panel = new mdui.Panel($element.find('.mdui-panel'), { accordion: true });

    $element.on('scroll', (e) => {
      if (e.target.scrollTop) {
        $appbar.addClass('is-top');
      } else {
        $appbar.removeClass('is-top');
      }
    });

    panel.$collapse.find('.mdui-panel-item')
      .on('open.mdui.panel', function () {
        const $this = $(this);

        $this.addClass('item-open');
        $this.prev().addClass('item-next-open');
        $this.next().addClass('item-prev-open');
      })
      .on('close.mdui.panel', function () {
        const $this = $(this);

        $this.addClass('item-close').removeClass('item-open');
        $this.prev().addClass('item-next-close').removeClass('item-next-open');
        $this.next().addClass('item-prev-close').removeClass('item-prev-open');
      })
      .on('closed.mdui.panel', function () {
        const $this = $(this);

        $this.removeClass('item-close');
        $this.prev().removeClass('item-next-close');
        $this.next().removeClass('item-prev-close');
      });
  },

  /**
   * 销毁前
   */
  destroy: () => {
  },

  /**
   * 响应输入框输入事件
   */
  input: e => ({
    [e.target.name]: e.target.value,
  }),
});
