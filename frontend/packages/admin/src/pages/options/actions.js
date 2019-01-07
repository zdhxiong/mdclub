import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Option, Email } from 'mdclub-sdk-js';
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

    // 滚动时，应用栏添加阴影
    $(props.element).on('scroll', (e) => {
      global_actions.lazyComponents.appBar.setState({ shadow: !!e.target.scrollTop });
    });

    actions.setState({ loading: true });

    // 加载初始数据
    Option.getAll((response) => {
      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        data: response.data,
        loading: false,
      });
    });
  },

  /**
   * 初始化可扩展面板
   * @param props
   * @returns {Function}
   */
  initPanel: (props) => {
    const panel = new mdui.Panel(props.element, { accordion: true });

    panel.$collapse.find('.mdui-panel-item')
      .on('open.mdui.panel', function () {
        const $this = $(this);

        $this.addClass('item-open');
        $this.prev().addClass('item-next-open');
        $this.next().addClass('item-prev-open');
      })
      .on('opened.mdui.panel', function () {
        $(this).addClass('item-opened');
      })
      .on('close.mdui.panel', function () {
        const $this = $(this);

        $this.addClass('item-close').removeClass('item-open item-opened');
        $this.prev().addClass('item-next-close').removeClass('item-next-open');
        $this.next().addClass('item-prev-close').removeClass('item-prev-open');
      })
      .on('closed.mdui.panel', function () {
        const $this = $(this);

        $this.removeClass('item-close');
        $this.prev().removeClass('item-next-close');
        $this.next().removeClass('item-prev-close');
      });

    mdui.mutation();
  },

  /**
   * 销毁前
   */
  destroy: (props) => {
    $(props.element).off('scroll');
  },

  /**
   * 响应输入框输入事件
   */
  data: {
    input: e => ({
      [e.target.name]: e.target.value,
    }),
  },

  /**
   * 发送测试邮件
   */
  sendTestEmail: () => (state, actions) => {
    mdui.prompt('请输入用于接收测试邮件的邮箱地址', (email) => {
      const emailData = {
        email,
        subject: `${state.data.site_name}的测试邮件`,
        content: '你收到了这封邮件，表示你的邮件服务器已设置成功',
      };

      const waitAlert = mdui.alert('正在发送邮件，请稍候…');

      Email.send(emailData, (response) => {
        waitAlert.close();

        if (response.code === 100002) {
          mdui.alert(Object.values(response.errors).join('<br/>'), response.message);
          return;
        }

        if (response.code) {
          mdui.alert(response.extra_message, response.message);
          return;
        }

        mdui.alert(`请登录邮箱：${email}，查看是否收到了测试邮件`);
      });
    });
  },

  /**
   * 提交设置
   */
  submit: e => (state, actions) => {
    e.preventDefault();

    actions.setState({ submitting: true });

    Option.updateMultiple(state.data, (response) => {
      actions.setState({ submitting: false });

      if (response.code) {
        mdui.alert(response.message);
        return;
      }

      mdui.snackbar('保存成功');
    });
  },
});
